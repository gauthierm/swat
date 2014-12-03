<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\Util;

/**
 * A menu control where menu items are grouped together
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see MenuGroup
 */
class GroupedMenu extends AbstractMenu implements UIParent
{
    // {{{ protected properties

    /**
     * The set of MenuGroup objects contained in this grouped menu
     *
     * @var array
     */
    protected $groups = array();

    // }}}
    // {{{ public function addGroup()

    /**
     * Adds a group to this grouped menu
     *
     * @param MenuGroup $group the group to add.
     */
    public function addGroup(MenuGroup $group)
    {
        $this->groups[] = $group;
        $group->parent = $this;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child object
     *
     * This method fulfills the {@link UIParent} interface. It is used
     * by {@link Loader} when building a widget tree and should not need to be
     * called elsewhere. To add a menu group to a grouped menu, use
     * {@link GroupedMenu::addGroup()}.
     *
     * @param MenuGroup $child the child object to add.
     *
     * @throws Exception\InvalidClassException
     *
     * @see UIParent
     * @see GroupedMenu::addGroup()
     */
    public function addChild(Object $child)
    {
        if ($child instanceof MenuGroup) {
            $this->addGroup($child);
        } else {
            throw new Exception\InvalidClassException(
                'Only MenuGroup objects may be nested within a GroupedMenu '.
                'object.',
                0,
                $child
            );
        }
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this grouped menu
     */
    public function init()
    {
        parent::init();
        foreach ($this->groups as $group)
            $group->init();
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this grouped menu
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        $displayed_classes = array();

        $div_tag = new Html\Tag('div');
        $div_tag->id = $this->id;
        $div_tag->class = 'yuimenu';
        $div_tag->open();

        echo '<div class="bd">';

        $first = true;
        foreach ($this->groups as $group) {
            if ($first) {
                $group->display(true);
                $first = false;
            } else {
                $group->display();
            }
        }

        echo '</div>';

        $div_tag->close();

        if ($this->parent === null || !$this->parent instanceof MenuItem) {
            Util\JavaScript::displayInline($this->getInlineJavaScript());
        }
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
     * @return array the descendant UI-objects of this grouped menu. If
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

        foreach ($this->groups as $group) {
            if ($class_name === null || $group instanceof $class_name) {
                if ($group->id === null)
                    $out[] = $group;
                else
                    $out[$group->id] = $group;
            }

            if ($group instanceof UIParent) {
                $out = array_merge($out, $group->getDescendants($class_name));
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
     * @return Object the first descendant UI-object or null if no matching
     *                descendant is found.
     *
     * @see UIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name))
            return null;

        $out = null;

        foreach ($this->groups as $group) {
            if ($group instanceof $class_name) {
                $out = $group;
                break;
            }

            if ($group instanceof UIParent) {
                $out = $group->getFirstDescendant($class_name);
                if ($out !== null)
                    break;
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this grouped menu.
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
     * grouped menu.
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

        foreach ($this->groups as $key => $group) {
            $copy_group = $group->copy($id_suffix);
            $copy_group->parent = $copy;
            $copy->groups[$key] = $copy_group;
        }

        return $copy;
    }

    // }}}
}

?>
