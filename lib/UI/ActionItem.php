<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;

/**
 * A single entry in a {@link Actions} widget
 *
 * @package   Swat
 * @copyright 2005-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see Actions
 */
class ActionItem extends Control implements UIParent
{
    // {{{ public properties

    /**
     * A unique identifier for this action item
     *
     * @var string
     */
    public $id;

    /**
     * A human readable title displayed for this item
     *
     * @var string
     */
    public $title = '';

    /**
     * A Widget associated with this action item
     *
     * @var Widget
     */
    public $widget = null;

    // }}}
    // {{{ public function init()

    /**
     * Initializes this action item
     *
     * This initializes the widget contained in this action item if there is
     * one.
     */
    public function init()
    {
        parent::init();
        if ($this->widget !== null) {
            $this->widget->init();
        }
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this item
     *
     * Calls this item's widget display method.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->widget->display();
    }

    // }}}
    // {{{ public function setWidget()

    /**
     * Sets the widget to use for this item
     *
     * Each ActionItem can have one associated Widget. This method sets the
     * widget for this item.
     *
     * @param Widget $widget the widget associated with this action.
     *
     * @throws Exception\Exception
     */
    public function setWidget(Widget $widget)
    {
        if ($this->widget !== null) {
            throw new Exception\Exception(
                'Loader: Only one widget can be nested within an ActionItem'
            );
        }

        $this->widget = $widget;
        $widget->parent = $this;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child object
     *
     * This method fulfills the {@link UIParent} interface. It is used by
     * {@link Loader} when building a widget tree and should not need to be
     * called elsewhere. To set the a widget in an action item, use
     * {@link ActionItem::setWidget()}.
     *
     * @param Widget $child a reference to a child object to add.
     *
     * @throws Exception\InvalidClassException
     *
     * @see UIParent
     * @see ActionItem::setWidget()
     */
    public function addChild(Object $child)
    {
        if ($child instanceof Widget) {
            $this->setWidget($child);
        } else {
            throw new Exception\InvalidClassException(
                'Only Widget objects may be nested within an ActionItem ' .
                'object.',
                0,
                $child
            );
        }
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this action item
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this action
     *                          item.
     *
     * @see Widget::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        if ($this->widget !== null) {
            $set->addEntrySet($this->widget->getHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this action item
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed
     *                          for this action item.
     *
     * @see Widget::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::geAvailabletHtmlHeadEntrySet();

        if ($this->widget !== null) {
            $set->addEntrySet($this->widget->getAvailableHtmlHeadEntrySet());
        }

        return $set;
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
     * @return array the descendant UI-objects of this action item. If
     *               descendant objects have identifiers, the identifier is
     *               used as the array key.
     *
     * @see UIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (!($class_name === null ||
            class_exists($class_name) || interface_exists($class_name))) {
            return array();
        }

        $out = array();

        if ($this->widget !== null) {
            if ($class_name === null || $this->widget instanceof $class_name) {
                if ($this->widget->id === null) {
                    $out[] = $this->widget;
                } else {
                    $out[$this->widget->id] = $this->widget;
                }
            }

            if ($this->widget instanceof UIParent) {
                $out = array_merge(
                    $out,
                    $this->widget->getDescendants($class_name)
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
        if (!class_exists($class_name) && !interface_exists($class_name)) {
            return null;
        }

        $out = null;

        if ($this->widget instanceof $class_name) {
            $out = $this->widget;
        }

        if ($out === null && $this->widget instanceof UIParent) {
            $out = $this->widget->getFirstDescendant($class_name);
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this action item.
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
     * action item.
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

        if ($this->widget !== null) {
            $copy_widget = $this->widget->copy($id_suffix);
            $copy_widget->parent = $copy;
            $copy->widget = $copy_widget;
        }

        return $copy;
    }

    // }}}
}
