<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\L;

/**
 * An item in a menu
 *
 * MenuItem objects may be added to {@link Menu} or {@link MenuGroup} widgets.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see Menu
 * @see MenuGroup
 */
class MenuItem extends Control implements UIParent
{
    // {{{ public properties

    /**
     * The URI-reference (see RFC2396) linked by this menu item
     *
     * If no link is specified, this menu item does not link to anything.
     *
     * Optionally uses vsprintf() syntax, for example:
     * <code>
     * $item->link = 'MySection/MyPage/%s?id=%s';
     * </code>
     *
     * @var string
     *
     * @see MenuItem::$value
     */
    public $link;

    /**
     * A value or array of values to substitute into the <i>link</i> property
     * of this menu item
     *
     * The value property may be specified either as an array of values or as
     * a single value. If an array is passed, a call to vsprintf() is done
     * on the {@link MenuItem::$link} property. If the value is a string a
     * single sprintf() call is made.
     *
     * @var array|string
     *
     * @see MenuItem::$link
     */
    public $value;

    /**
     * The user-visible title of this menu item
     *
     * @var string
     */
    public $title;

    /**
     * The stock id of this menu item
     *
     * Specifying a stock id initializes this menu item with a set of
     * stock values.
     *
     * @var string
     *
     * @see ToolLink::setFromStock()
     */
    public $stock_id = null;

    // }}}
    // {{{ protected properties

    /**
     * The sub menu of this menu item
     *
     * @var AbstractMenu
     *
     * @see MenuItem::setSubMenu()
     */
    protected $sub_menu;

    /**
     * A CSS class set by the stock_id of this menu item
     *
     * @var string
     */
    protected $stock_class = null;

    // }}}
    // {{{ public function setSubMenu()

    /**
     * Sets the sub-menu of this menu item
     *
     * @param AbstractMenu $menu the sub-menu for this menu item.
     */
    public function setSubMenu(AbstractMenu $menu)
    {
        $this->sub_menu = $menu;
        $menu->parent = $this;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child object
     *
     * This method fulfills the {@link UIParent} interface. It is used
     * by {@link Loader} when building a widget tree and should not need to be
     * called elsewhere. To set the sub-menu for a menu item, use
     * {@link MenuItem::setSubMenu()}.
     *
     * @param AbstractMenu $child the child object to add.
     *
     * @throws Exception\InvalidClassException
     * @throws Exception\Exception if this menu item already has a sub-menu.
     *
     * @see UIParent
     * @see MenuItem::setSubMenu()
     */
    public function addChild(Object $child)
    {
        if ($this->sub_menu === null) {
            if ($child instanceof AbstractMenu) {
                $this->setSubMenu($child);
            } else {
                throw new Exception\InvalidClassException(
                    'Only a AbstractMenu object may be nested within a '.
                    'MenuItem object.',
                    0,
                    $child
                );
            }
        } else {
            throw new Exception\Exception(
                'Can only add one sub-menu to a menu item.'
            );
        }
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this menu item
     */
    public function init()
    {
        parent::init();

        if ($this->stock_id !== null)
            $this->setFromStock($this->stock_id, false);

        if ($this->sub_menu !== null)
            $this->sub_menu->init();
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this menu item
     *
     * If this item has a sub-menu, the sub-menu is also displayed.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        if ($this->link === null) {
            $span_tag = new Html\Tag('span');
            $span_tag->id = $this->id;
            $span_tag->class = $this->getCSSClassString();
            $span_tag->setContent($this->title);
            $span_tag->display();
        } else {
            $anchor_tag = new Html\Tag('a');
            $anchor_tag->id = $this->id;
            $anchor_tag->class = $this->getCSSClassString();

            if ($this->value === null)
                $anchor_tag->href = $this->link;
            elseif (is_array($this->value))
                $anchor_tag->href = vsprintf($this->link, $this->value);
            else
                $anchor_tag->href = sprintf($this->link, $this->value);

            $anchor_tag->setContent($this->title);
            $anchor_tag->display();
        }

        $this->displaySubMenu();
    }

    // }}}
    // {{{ public function setFromStock()

    /**
     * Sets the values of this menu item to a stock type
     *
     * Valid stock type ids are:
     *
     * - create
     * - add
     * - edit
     * - delete
     * - preview
     * - change-order
     * - help
     * - print
     * - email
     *
     * @param string  $stock_id             the identifier of the stock type to
     *                                      use.
     * @param boolean $overwrite_properties whether to overwrite properties if
     *                                      they are already set.
     *
     * @throws Exception\UndefinedStockTypeException
     */
    public function setFromStock($stock_id, $overwrite_properties = true)
    {
        switch ($stock_id) {
        case 'create':
            $title = L::_('Create');
            $class = 'swat-menu-item-create';
            break;

        case 'add':
            $title = L::_('Add');
            $class = 'swat-menu-item-add';
            break;

        case 'edit':
            $title = L::_('Edit');
            $class = 'swat-menu-item-edit';
            break;

        case 'delete':
            $title = L::_('Delete');
            $class = 'swat-menu-item-delete';
            break;

        case 'preview':
            $title = L::_('Preview');
            $class = 'swat-menu-item-preview';
            break;

        case 'change-order':
            $title = L::_('Change Order');
            $class = 'swat-menu-item-change-order';
            break;

        case 'help':
            $title = L::_('Help');
            $class = 'swat-menu-item-help';
            break;

        case 'print':
            $title = L::_('Print');
            $class = 'swat-menu-item-print';
            break;

        case 'email':
            $title = L::_('Email');
            $class = 'swat-menu-item-email';
            break;

        default:
            throw new Exception\UndefinedStockTypeException(
                "Stock type with id of '{$stock_id}' not found.",
                0,
                $stock_id
            );
        }

        if ($overwrite_properties || ($this->title === null))
            $this->title = $title;

        $this->stock_class = $class;
    }

    // }}}
    // {{{ public function getDescendants()

    /**
     * Gets descendant UI-objects
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this menu item. If
     *               descendant objects have identifiers, the identifier is
     *               used as the array key.
     *
     * @see UIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (!($class_name === null ||
            class_exists($class_name) || interface_exists($class_name)))
            return array();

        $out = array();

        if ($this->sub_menu !== null) {
            if ($class_name === null ||
                $this->sub_menu instanceof $class_name) {
                if ($this->sub_menu->id === null)
                    $out[] = $this->sub_menu;
                else
                    $out[$this->sub_menu->id] = $this->sub_menu;
            }

            if ($this->sub_menu instanceof UIParent) {
                $out = array_merge(
                    $out,
                    $this->sub_menu->getDescendants($class_name)
                );
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getFirstDescendant()

    /**
     * Gets the first descendant UI-object of a specific class
     *
     * @param string $class_name class name to look for.
     *
     * @return Object the first descendant widget or null if no matching
     *                descendant is found.
     *
     * @see UIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name))
            return null;

        $out = null;

        if ($this->sub_menu instanceof $class_name)
            $out = $this->sub_menu;

        if ($out === null && $this->sub_menu instanceof UIParent) {
            $out = $this->sub_menu->getFirstDescendant($class_name);
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this menu item.
     *
     * @return array an array of UI-object states with UI-object identifiers as
     *               array keys.
     */
    public function getDescendantStates()
    {
        $states = array();

        $state = '\Silverorange\Swat\Model\State';
        foreach ($this->getDescendants($state) as $id => $object) {
            $states[$id] = $object->getState();
        }

        return $states;
    }

    // }}}
    // {{{ public function setDescendantStates()

    /**
     * Sets descendant states
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * menu item.
     *
     * @param array $states an array of UI-object states with UI-object
     *                      identifiers as array keys.
     */
    public function setDescendantStates(array $states)
    {
        $state = '\Silverorange\Swat\Model\State';
        foreach ($this->getDescendants($state) as $id => $object) {
            if (isset($states[$id])) {
                $object->setState($states[$id]);
            }
        }
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

        if ($this->sub_menu !== null) {
            $copy_sub_menu = $this->sub_menu->copy($id_suffix);
            $copy_sub_menu->parent = $copy;
            $copy->sub_menu = $copy_sub_menu;
        }

        return $copy;
    }

    // }}}
    // {{{ protected function displaySubMenu()

    /**
     * Displays this menu item's sub-menu
     */
    protected function displaySubMenu()
    {
        if ($this->sub_menu !== null)
            $this->sub_menu->display();
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this menu item
     *
     * @return array the array of CSS classes that are applied to this menu
     *               item.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-menu-item');

        if ($this->stock_class !== null)
            $classes[] = $this->stock_class;

        $classes = array_merge($classes, parent::getCSSClassNames());

        return $classes;
    }

    // }}}
}

?>
