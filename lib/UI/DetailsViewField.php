<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Exception;
use Silverorange\Swat\L;

/**
 * A visible field in a DetailsView
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DetailsViewField extends CellRendererContainer
{
    // {{{ public properties

    /**
     * The unique identifier of this field
     *
     * @var string
     */
    public $id = null;

    /**
     * The title of this field
     *
     * @var string
     */
    public $title = '';

    /**
     * Optional content type for the title
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $title_content_type = 'text/plain';

    /**
     * Whether or not this field is displayed
     *
     * @var boolean
     */
    public $visible = true;

    /**
     * Whether or not to show a colon after the title of this details view field
     *
     * By default, a colon is shown.
     *
     * @var boolean
     */
    public $show_colon = true;

    /**
     * Whether or not to include CSS classes from the first cell renderer
     * of this field in this field's CSS classes
     *
     * @see DetailsViewField::getCSSClassNames()
     */
    public $show_renderer_classes = true;

    // }}}
    // {{{ protected properties

    /**
     * Whether or not this field is odd or even in its parent details view
     *
     * @var boolean
     */
    protected $odd = false;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new details view field
     *
     * @param string $id an optional unique ideitifier for this details view
     *                   field.
     */
    public function __construct($id = null)
    {
        $this->id = $id;
        parent::__construct();
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this field
     *
     * This calls init on all renderers in this field.
     */
    public function init()
    {
        foreach ($this->renderers as $renderer) {
            $renderer->init();
        }
    }

    // }}}
    // {{{ public function process()

    public function process()
    {
        foreach ($this->renderers as $renderer) {
            $renderer->process();
        }
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this details view field using a data object
     *
     * @param mixed   $data a data object used to display the cell renderers in
     *                      this field.
     * @param boolean $odd  whether this is an odd or even field so alternating
     *                      style can be applied.
     */
    public function display($data, $odd)
    {
        if (!$this->visible) {
            return;
        }

        $this->odd = $odd;

        $tr_tag = new Html\Tag('tr');
        $tr_tag->id = $this->id;
        $tr_tag->class = $this->getCSSClassString();

        $tr_tag->open();
        $this->displayHeader();
        $this->displayValue($data);
        $tr_tag->close();
    }

    // }}}
    // {{{ public function displayHeader()

    /**
     * Displays the header for this details view field
     */
    public function displayHeader()
    {
        $th_tag = new Html\Tag('th');
        $th_tag->scope = 'row';
        if ($this->title == '') {
            $th_tag->setContent('&nbsp;');
        } else {
            $th_tag->setContent(
                $this->getHeaderTitle(),
                $this->title_content_type
            );
        }

        $th_tag->display();
    }

    // }}}
    // {{{ public function displayValue()

    /**
     * Displays the value of this details view field
     *
     * The properties of the cell renderers are set from the data object
     * through the datafield property mappings.
     *
     * @param mixed $data the data object to display in this field.
     */
    public function displayValue($data)
    {
        if (count($this->renderers) === 0) {
            throw new Exception\Exception(
                'No renderer has been provided for this field.'
            );
        }

        $sensitive = $this->parent->isSensitive();

        // Set the properties of the renderers to the value of the data field.
        foreach ($this->renderers as $renderer) {
            $this->renderers->applyMappingsToRenderer($renderer, $data);
            $renderer->sensitive = $renderer->sensitive && $sensitive;
        }

        $this->displayRenderers($data);
    }

    // }}}
    // {{{ public function getTdAttributes()

    /**
     * Gets the TD tag attributes for this column
     *
     * The returned array is of the form 'attribute' => value.
     *
     * @return array an array of attributes to apply to this column's TD tag.
     */
    public function getTdAttributes()
    {
        return array(
            'class' => $this->getCSSClassString(),
        );
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this field
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this
     *                          details-view field.
     *
     * @see Object::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        $renderers = $this->getRenderers();
        foreach ($renderers as $renderer) {
            $set->addEntrySet($renderer->getHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this details-view
     * field
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed by
     *                          this details-view field.
     *
     * @see Object::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        $renderers = $this->getRenderers();
        foreach ($renderers as $renderer) {
            $set->addEntrySet($renderer->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ protected function getHeaderTitle()

    /**
     * Gets the title to use for the header of this details view field.
     *
     * @return string the title to use for the header.
     *
     * @see DetailsViewField::displayHeader()
     */
    protected function getHeaderTitle()
    {
        if ($this->title == '') {
            $header_title = '&nbsp;';
        } else {
            $header_title = ($this->show_colon)
                ? sprintf(L::_('%s:'), $this->title)
                : $this->title;
        }

        return $header_title;
    }

    // }}}
    // {{{ protected function displayRenderers()

    /**
     * Renders each cell renderer in this details-view field
     *
     * @param mixed $data the data object being used to render the cell
     *                    renderers of this field.
     */
    protected function displayRenderers($data)
    {
        $td_tag = new Html\Tag('td', $this->getTdAttributes());
        $td_tag->open();

        $first = true;
        foreach ($this->renderers as $renderer) {
            if ($first) {
                $first = false;
            } else {
                echo ' ';
            }
            $renderer->render();
        }

        $td_tag->close();
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this details-view
     * field
     *
     * CSS classes are added to this field in the following order:
     *
     * 1. hard-coded CSS classes from field subclasses,
     * 2. 'odd' if this is an odd row in the parent view,
     * 3. user-specified CSS classes on this field,
     *
     * If {@link DetailsViewField::$show_renderer_classes} is true, the
     * following extra CSS classes are added:
     *
     * 4. the inheritance classes of the first cell renderer in this field,
     * 5. hard-coded CSS classes from the first cell renderer in this field,
     * 6. hard-coded data-specific CSS classes from the first cell renderer in
     *    this field if this field has data mappings applied,
     * 7. user-specified CSS classes on the first cell renderer in this field.
     *
     * @return array the array of CSS classes that are applied to this
     *               details-view field.
     *
     * @see CellRenderer::getInheritanceCSSClassNames()
     * @see CellRenderer::getBaseCSSClassNames()
     * @see Object::getCSSClassNames()
     */
    protected function getCSSClassNames()
    {
        // base classes
        $classes = $this->getBaseCSSClassNames();

        // odd
        if ($this->odd) {
            $classes[] = 'odd';
        }

        // user-specified classes
        $classes = array_merge($classes, $this->classes);

        $first_renderer = $this->renderers->getFirst();
        if ($this->show_renderer_classes &&
            $first_renderer instanceof CellRenderer) {

            // renderer inheritance classes
            $classes = array_merge(
                $classes,
                $first_renderer->getInheritanceCSSClassNames()
            );

            // renderer base classes
            $classes = array_merge(
                $classes,
                $first_renderer->getBaseCSSClassNames()
            );

            // renderer data specific classes
            if ($this->renderers->mappingsApplied()) {
                $classes = array_merge(
                    $classes,
                    $first_renderer->getDataSpecificCSSClassNames()
                );
            }

            // renderer user-specified classes
            $classes = array_merge($classes, $first_renderer->classes);
        }

        return $classes;
    }

    // }}}
    // {{{ protected function getBaseCSSClassNames()

    /**
     * Gets the base CSS class names of this details-view field
     *
     * This is the recommended place for field subclasses to add extra hard-
     * coded CSS classes.
     *
     * @return array the array of base CSS class names for this details-view
     *               field.
     */
    protected function getBaseCSSClassNames()
    {
        return array('swat-details-view-field');
    }

    // }}}
}
