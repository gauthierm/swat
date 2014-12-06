<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Util;

/**
 * Fieldset tag container
 *
 * An HTML fieldset tag with an optional HTML legend title.
 *
 * @package   Swat
 * @copyright 2004-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Fieldset extends DisplayableContainer implements Titleable
{
    // {{{ public properties

    /**
     * Fieldset title
     *
     * A visible title for this fieldset, or null.
     *
     * @var string
     */
    public $title = null;

    /**
     * Optional content type for the title
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $title_content_type = 'text/plain';

    /**
     * Access key
     *
     * Access key for this fieldset legend, for keyboard nagivation.
     *
     * @var string
     */
    public $access_key = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new fieldset
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        // JavaScript for IE peekaboo hack
        $yui = new Html\YUI(array('event'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript('packages/swat/javascript/swat-fieldset.js');
    }

    // }}}
    // {{{ public function getTitle()

    /**
     * Gets the title of this fieldset
     *
     * Implements the {Titleable::getTitle()} interface.
     *
     * @return the title of this fieldset.
     */
    public function getTitle()
    {
        return $this->title;
    }

    // }}}
    // {{{ public function getTitleContentType()

    /**
     * Gets the title content-type of this fieldset
     *
     * Implements the {@link Titleable::getTitleContentType()} interface.
     *
     * @return string the title content-type of this fieldset.
     */
    public function getTitleContentType()
    {
        return $this->title_content_type;
    }

    // }}}
    // {{{ public function display()

    public function display()
    {
        if (!$this->visible)
            return;

        Widget::display();

        $fieldset_tag = new Html\Tag('fieldset');
        $fieldset_tag->id = $this->id;
        $fieldset_tag->class = $this->getCSSClassString();
        $fieldset_tag->open();

        if ($this->title !== null) {
            $legend_tag = new Html\Tag('legend');

            if ($this->access_key != '')
                $legend_tag->accesskey = $this->access_key;

            $legend_tag->setContent($this->title, $this->title_content_type);
            $legend_tag->display();
        }

        $this->displayChildren();

        Util\JavaScript::displayInline($this->getInlineJavascript());

        $fieldset_tag->close();
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets fieldset specific inline JavaScript
     *
     * @return string fieldset specific inline JavaScript.
     */
    protected function getInlineJavaScript()
    {
        return sprintf("var %s_obj = new SwatFieldset('%s');",
            $this->id, $this->id);
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this fieldset
     *
     * @return array the array of CSS classes that are applied to this fieldset.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-fieldset');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}

?>
