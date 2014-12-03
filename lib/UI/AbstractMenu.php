<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Util;

/**
 * Abstract base class for menus in Swat
 *
 * Menu in Swat make use of the YUI menu widget and its progressive enhancement
 * features. Swat menus are always positioned statically. See the
 * {@link http://developer.yahoo.com/yui/docs/YAHOO.widget.Menu.html#position
 * YUI menu documentation} for what this means.
 *
 * @package   Swat
 * @copyright 2007-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see Menu
 * @see GroupedMenu
 */
abstract class AbstractMenu extends Control
{
    // {{{ public properties

    /**
     * Whether or not a mouse click outside this menu will hide this menu
     *
     * Defaults to true.
     *
     * @var boolean
     */
    public $click_to_hide = true;

    /**
     * Whether or not sub-menus of this menu will automatically display on
     * mouse-over
     *
     * Defaults to true. Set to false to require clicking on a menu item to
     * display a sub-menu.
     *
     * @var boolean
     */
    public $auto_sub_menu_display = true;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new menu object
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $yui = new Html\YUI(array('menu'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addStyleSheet('packages/swat/styles/swat-menu.css');
    }

    // }}}
    // {{{ public function setMenuItemValues()

    /**
     * Sets the value of all {@link MenuItem} objects within this menu
     *
     * This is usually easier than setting all the values manually if the
     * values are dynamic.
     *
     * @param string $value
     */
    public function setMenuItemValues($value)
    {
        $items = $this->getDescendants('\Silverorange\Swat\UI\MenuItem');
        foreach ($items as $item) {
            $item->value = $value;
        }
    }

    // }}}
    // {{{ protected function getJavaScriptClass()

    /**
     * Gets the name of the JavaScript class to instantiate for this menu
     *
     * Sub-classes of this class may want to return a sub-class of the default
     * JavaScript menu class.
     *
     * @return string the name of the JavaScript class to instantiate for this
     *                menu. Defaults to 'YAHOO.widget.Menu'.
     */
    protected function getJavaScriptClass()
    {
        return 'YAHOO.widget.Menu';
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets the inline JavaScript used by this menu control
     *
     * @return string the inline JavaScript used by this menu control.
     */
    protected function getInlineJavaScript()
    {
        // cast to boolean
        $click_to_hide = !!$this->click_to_hide;
        $auto_sub_menu_display = !!$this->auto_sub_menu_display;

        $parameters = array(
            'clicktohide'        => $click_to_hide,
            'autosubmenudisplay' => $auto_sub_menu_display,
            'position'           => 'static',
        );

        $parameters = json_encode($parameters);

        return sprintf(
            "YAHOO.util.Event.onContentReady(\n".
            "\t%s,\n".
            "\tfunction()\n".
            "\t{\n".
            "\t\tvar menu_obj = new %s(%s, %s);\n".
            "\t\tmenu_obj.render();\n".
            "\t\tmenu_obj.show();\n".
            "\t}\n".
            ");\n",
            Util\JavaScript::quoteString($this->id),
            $this->getJavaScriptClass(),
            Util\JavaScript::quoteString($this->id),
            $parameters
        );
    }

    // }}}
}
