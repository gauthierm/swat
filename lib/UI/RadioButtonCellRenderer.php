<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Exception;

/**
 * A view selector cell renderer displayed as a radio button
 *
 * Only one row may be selected by this selector. If you need to select
 * multiple rows, use {@link CheckboxCellRenderer}.
 *
 * @package   Swat
 * @copyright 2007-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       ViewSelector
 */
class RadioButtonCellRenderer extends CellRenderer implements ViewSelector
{
    // {{{ public properties

    /**
     * Identifier of this radio button cell renderer
     *
     * Identifier must be unique within this cell renderer's parent cell
     * renderer container. This property is required and can not be a
     * data-mapped value.
     *
     * @var string
     */
    public $id;

    /**
     * Value of this cell's radio button
     *
     * This property is intended to be data-mapped to the current row
     * identifier in a record set.
     *
     * @var string
     */
    public $value;

    /**
     * Optional title of the label for the rendered radio button
     *
     * If no title is specified (default) there is no label displayed with
     * the  radio button.
     *
     * @var string
     */
    public $title;

    /**
     * Optional content type for radio button label title
     *
     * Defaults to text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    // }}}
    // {{{ private properties

    /**
     * The selected value populated during the processing of this cell
     * renderer
     *
     * This property is used to track the selected state of radio buttons when
     * rendering for a particular value.
     *
     * @var array
     */
    private $selected_value;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new radio button cell renderer
     */
    public function __construct()
    {
        parent::__construct();

        $this->makePropertyStatic('id');

        $yui = new Html\YUI(array('dom'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript(
            'packages/swat/javascript/swat-radio-button-cell-renderer.js'
        );

        // auto-generate an id to use if no id is set
        $this->id = $this->getUniqueId();
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this radio button cell renderer
     */
    public function process()
    {
        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $data = $form->getFormData();
            if (isset($data[$this->id])) {
                $this->selected_value = $data[$this->id];

                $view = $this->getFirstAncestor('\Silverorange\Swat\UI\View');
                if ($view !== null) {
                    $selection = new ViewSelection(
                        array($this->selected_value)
                    );
                    $view->setSelection($selection, $this);
                }
            }
        }
    }

    // }}}
    // {{{ public function render()

    /**
     * Renders this radio button cell renderer
     */
    public function render()
    {
        if (!$this->visible)
            return;

        parent::render();

        if ($this->title !== null) {
            $label_tag = new Html\Tag('label');
            $label_tag->for = $this->id.'_radio_button_'.$this->value;
            $label_tag->setContent($this->title, $this->content_type);
            $label_tag->open();
        }

        $radio_button_tag = new Html\Tag('input');
        $radio_button_tag->type = 'radio';
        $radio_button_tag->name = $this->id;
        $radio_button_tag->id = $this->id.'_radio_button_'.$this->value;
        $radio_button_tag->value = $this->value;
        if (!$this->sensitive)
            $radio_button_tag->disabled = 'disabled';

        $view = $this->getFirstAncestor('\Silverorange\Swat\UI\View');
        if ($view !== null) {
            $selection = $view->getSelection($this);
            if ($selection->contains($this->value))
                $radio_button_tag->checked = 'checked';
        }

        echo '<span class="swat-radio-wrapper">';
        $radio_button_tag->display();
        echo '<span class="swat-radio-shim"></span>';
        echo '</span>';

        if ($this->title !== null) {
            $label_tag->displayContent();
            $label_tag->close();
        }
    }

    // }}}
    // {{{ public function getId()

    /**
     * Gets the identifier of this checkbox cell renderer
     *
     * Satisfies the {@link ViewSelector} interface.
     *
     * @return string the identifier of this checkbox cell renderer.
     */
    public function getId()
    {
        return $this->id;
    }

    // }}}
    // {{{ public function getInlineJavaScript()

    /**
     * Gets the inline JavaScript required by this radio button cell renderer
     *
     * @return string the inline JavaScript required by this radio button cell
     *                renderer.
     */
    public function getInlineJavaScript()
    {
        $view = $this->getFirstAncestor('\Silverorange\Swat\UI\View');
        if ($view !== null) {
            $javascript = sprintf(
                "var %s = new SwatRadioButtonCellRenderer('%s', %s);",
                $this->id, $this->id, $view->id);
        } else {
            $javascript = '';
        }

        return $javascript;
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                          objects in the UI tree.
     *
     * @return Object a deep copy of the UI tree starting with this UI object.
     *
     * @see Object::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);

        if ($id_suffix != '')
            $copy->id = $copy->id.$id_suffix;

        return $copy;
    }

    // }}}
    // {{{ private function getForm()

    /**
     * Gets the form this radio button cell renderer is contained in
     *
     * @return Form the form this radio button cell renderer is contained
     *                  in.
     *
     * @throws Exception\Exception if this radio button cell renderer does not
     *         have a Form ancestor.
     */
    private function getForm()
    {
        $form = $this->getFirstAncestor('\Silverorange\Swat\UI\Form');

        if ($form === null) {
            throw new Exception\Exception(
                'RadioButtonCellRenderer must have a Form ancestor in the '.
                'UI tree.'
            );
        }

        return $form;
    }

    // }}}
}

?>
