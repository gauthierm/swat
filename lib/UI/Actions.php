<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\L;

/**
 * Actions widget
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Actions extends Control implements UIParent
{
    // {{{ public properties

    /**
     * Selected action
     *
     * The currently selected action item, or null.
     *
     * @var ActionItem
     */
    public $selected = null;

    /**
     * Show blank
     *
     * Whether to show an inital blank option in the flydown.
     *
     * @var boolean
     */
    public $show_blank = true;

    /**
     * Auto-reset
     *
     * Whether to auto reset the action flydown to the default action
     * after processing.
     *
     * @var boolean
     */
    public $auto_reset = true;

    // }}}
    // {{{ protected properties

    /**
     * The available actions for this actions selector indexed by id
     *
     * This array only contains actions that have a non-null id.
     *
     * @var array
     */
    protected $action_items_by_id = array();

    // }}}
    // {{{ private properties

    /**
     * The available actions for this actions selector.
     *
     * @var array
     */
    private $action_items = array();

    /**
     * The view containing items acted upon by this actions control
     *
     * @var View
     *
     * @see Actions::setViewSelector()
     */
    private $view;

    /**
     * The selector used to select items acted upon by this actions control
     *
     * @var ViewSelector
     *
     * @see Actions::setViewSelector()
     */
    private $selector;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new actions list
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $yui = new Html\YUI(array('dom', 'event', 'animation'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript('packages/swat/javascript/swat-actions.js');
        $this->addStyleSheet('packages/swat/styles/swat-actions.css');
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this action item
     *
     * This initializes the action items contained in this actions list.
     */
    public function init()
    {
        parent::init();

        foreach ($this->action_items as $action_item)
            $action_item->init();
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this list of actions
     *
     * Internal widgets are automatically created if they do not exist.
     * Javascript is displayed, then the display methods of the internal
     * widgets are called.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        $flydown = $this->getCompositeWidget('action_flydown');
        foreach ($this->action_items as $item) {
            if ($item->visible) {
                if ($item instanceof ActionItemDivider) {
                    $flydown->addDivider();
                } else {
                    $flydown->addOption($item->id, $item->title);
                }
            }
        }

        // set the flydown back to its initial state (no persistence). The
        // flydown is never reset if there is a selected item and the selected
        // items has a widget with one or more messages.
        if ($this->auto_reset &&
            ($this->selected === null || $this->selected->widget === null ||
            !$this->selected->widget->hasMessage())) {
            $flydown->reset();
        }

        // select the current action item based upon the flydown value
        if (isset($this->action_items_by_id[$flydown->value]))
            $this->selected = $this->action_items_by_id[$flydown->value];
        else
            $this->selected = null;

        $div_tag = new Html\Tag('div');
        $div_tag->id = $this->id;
        $div_tag->class = $this->getCSSClassString();
        $div_tag->open();

        echo '<div class="swat-actions-controls">';

        $label = new Html\Tag('label');
        $label->for = $flydown->getFocusableHtmlId();
        $label->setContent(L::_('Action: '));
        $label->display();

        $flydown->display();
        echo ' ';
        $this->displayButton();

        echo '</div>';

        foreach ($this->action_items as $item) {
            if ($item->widget !== null) {
                $div = new Html\Tag('div');

                $div->class = ($item == $this->selected) ?
                    'swat-visible' : 'swat-hidden';

                $div->id = $this->id.'_'.$item->id;

                $div->open();
                $item->display();
                $div->close();
            }
        }

        echo '<div class="swat-actions-note">';
        echo L::_('Actions apply to checked items.');
        echo '</div>';

        $div_tag->close();

        Util\JavaScript::displayInline($this->getInlineJavaScript());
    }

    // }}}
    // {{{ public function process()

    /**
     * Figures out what action item is selected
     *
     * This method creates internal widgets if they do not exist, and then
     * determines what ActionItem was selected by the user by calling the
     * process methods of the internal widgets.
     */
    public function process()
    {
        parent::process();

        $flydown = $this->getCompositeWidget('action_flydown');
        $selected_id = $flydown->value;

        if (isset($this->action_items_by_id[$selected_id])) {
            $this->selected = $this->action_items_by_id[$selected_id];

            if ($this->selected->widget !== null)
                $this->selected->widget->process();

        } else {
            $this->selected = null;
        }
    }

    // }}}
    // {{{ public function addActionItem()

    /**
     * Adds an action item to this widget
     *
     * @param ActionItem $item a reference to the item to add.
     *
     * @see ActionItem
     */
    public function addActionItem(ActionItem $item)
    {
        $this->action_items[] = $item;
        $item->parent = $this;

        if ($item->id !== null)
            $this->action_items_by_id[$item->id] = $item;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child object
     *
     * This method fulfills the {@link UIParent} interface. It is used by
     * {@link Loader} when building a widget tree and should not need to be
     * called elsewhere. To add an action item to an actions object use
     * {@link Actions::addActionItem()}.
     *
     * @param ActionItem $child a reference to a child object to add.
     *
     * @throws Exception\InvalidClassException
     *
     * @see UIParent
     * @see Actions::addActionItem()
     */
    public function addChild(Object $child)
    {
        if ($child instanceof ActionItem) {
            $this->addActionItem($child);
        } else {
            throw new Exception\InvalidClassException(
                'Only ActionItem objects may be nested within an Action '.
                'object.',
                0,
                $child
            );
        }
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this actions list
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this
     *                          actions list.
     *
     * @see Widget::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        foreach ($this->action_items as $child_widget)
            $set->addEntrySet($child_widget->getHtmlHeadEntrySet());

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this actions list
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed by
     *                          this actions list.
     *
     * @see Widget::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        foreach ($this->action_items as $child_widget) {
            $set->addEntrySet($child_widget->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getActionItems()

    /**
     * Gets the array of current actions
     *
     * @return array of ActionItems
     *
     * @see ActionItem
     */
    public function getActionItems()
    {
        return $this->action_items;
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
     * @return array the descendant UI-objects of this actions widget. If
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

        foreach ($this->action_items as $action_item) {
            if ($class_name === null || $action_item instanceof $class_name) {
                if ($action_item->id === null)
                    $out[] = $action_item;
                else
                    $out[$action_item->id] = $action_item;
            }

            if ($action_item instanceof UIParent) {
                $out = array_merge(
                    $out,
                    $action_item->getDescendants($class_name)
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

        foreach ($this->action_items as $action_item) {
            if ($action_item instanceof $class_name) {
                $out = $action_item;
                break;
            }

            if ($action_item instanceof UIParent) {
                $out = $action_item->getFirstDescendant($class_name);
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
     * subtree below this actions widget.
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
     * actions widget.
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
    // {{{ public function setViewSelector()

    /**
     * Sets the optional view and selector of this actions control
     *
     * If a view and selector are specified for this actions control,
     * submitting the form is prevented until one or more items in the view
     * are selected by the selector.
     *
     * @param View         $view     the view items must be selected in.
     *                               Specify null to remove any existing view
     *                               selector.
     * @param ViewSelector $selector optional. The selector in the view that
     *                               must select the items. If not specified,
     *                               the first selector in the view is used.
     *
     * @throws Exception\Exception if no selector is specified and the
     *         specified view does not have a selector.
     */
    public function setViewSelector(View $view, ViewSelector $selector = null)
    {
        if ($view === null) {
            $selector = null;
        } else {
            if ($selector === null) {
                $selector_class = '\Silverorange\Swat\UI\ViewSelector';
                $selector = $view->getFirstDescendant($selector_class);
            }

            if ($selector === null) {
                throw new Exception\Exception(
                    'No selector was specified and view does not have a '.
                    'selector'
                );
            }
        }

        $this->view = $view;
        $this->selector = $selector;
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
        $copy->action_items_by_id = array();

        foreach ($this->action_items as $key => $action_item) {
            $copy_action_item = $action_item->copy($id_suffix);
            $copy_action_item->parent = $copy;
            $copy->action_items[$key] = $copy_action_item;
            if ($copy_action_item->id !== null) {
                $copy->action_items_by_id[$copy_action_item->id] =
                    $copy_action_item;
            }
        }

        return $copy;
    }

    // }}}
    // {{{ public function hasMessage()

    /**
     * Checks for the presence of messages
     *
     * @return boolean true if this widget or the selected action's widget has
     *                 one or more messages.
     *
     * @see Widget::hasMessage()
     */
    public function hasMessage()
    {
        $has_message = parent::hasMessage();

        if ($this->selected !== null && $this->selected->widget !== null &&
            $this->selected->widget->hasMessage()) {
            $has_message = true;
        }

        return $has_message;
    }

    // }}}
    // {{{ protected function displayButton()

    /**
     * Displays the button for this action list
     *
     * Subclasses may override this method to display more buttons.
     */
    protected function displayButton()
    {
        $button = $this->getCompositeWidget('apply_button');
        $button->setFromStock('apply');
        $button->display();
    }

    // }}}
    // {{{ protected function createCompositeWidgets()

    /**
     * Creates and the composite flydown and button widgets of this actions
     * control
     */
    protected function createCompositeWidgets()
    {
        $flydown = new Flydown($this->id.'_action_flydown');
        $flydown->show_blank = $this->show_blank;

        $this->addCompositeWidget($flydown, 'action_flydown');

        $button = new Button($this->id.'_apply_button');
        $this->addCompositeWidget($button, 'apply_button');
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets inline JavaScript required to show and hide selected action items
     *
     * @return string inline JavaScript required to show and hide selected
     *                action items.
     */
    protected function getInlineJavaScript()
    {
        static $shown = false;

        if (!$shown) {
            $javascript = $this->getInlineJavaScriptTranslations();
            $shown = true;
        } else {
            $javascript = '';
        }

        $values = array();
        if ($this->show_blank)
            $values[] = "''";

        foreach ($this->action_items as $item) {
            if ($item->visible) {
                $values[] = Util\JavaScript::quoteString($item->id);
            }
        }

        $selected_value = ($this->selected === null) ?
            'null' : Util\JavaScript::quoteString($this->selected->id);

        $javascript.= sprintf("var %s_obj = new SwatActions(%s, [%s], %s);",
            $this->id,
            Util\JavaScript::quoteString($this->id),
            implode(', ', $values),
            $selected_value);


        if ($this->view !== null && $this->selector !== null) {
            $javascript.= sprintf("\n%s_obj.setViewSelector(%s, '%s');",
                $this->id, $this->view->id, $this->selector->getId());
        }

        return $javascript;
    }

    // }}}
    // {{{ protected function getInlineJavaScriptTranslations()

    /**
     * Gets translatable string resources for the JavaScript object for
     * this widget
     *
     * @return string translatable JavaScript string resources for this widget.
     */
    protected function getInlineJavaScriptTranslations()
    {
        $dismiss_text  = L::_('Dismiss message.');
        $select_an_action_text = L::_('Please select an action.');
        $select_an_item_text = L::_('Please select one or more items.');
        $select_an_item_and_an_action_text =
            L::_('Please select an action, and one or more items.');

        return sprintf(
            "SwatActions.dismiss_text = '%s';\n".
            "SwatActions.select_an_action_text = '%s';\n".
            "SwatActions.select_an_item_text = '%s';\n".
            "SwatActions.select_an_item_and_an_action_text = '%s';\n",
            $dismiss_text,
            $select_an_action_text,
            $select_an_item_text,
            $select_an_item_and_an_action_text);
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this actions list
     *
     * @return array the array of CSS classes that are applied to this actions
     *               list.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-actions');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
