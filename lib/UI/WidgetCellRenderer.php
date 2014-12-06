<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;

/**
 *
 * @package   Swat
 * @copyright 2006-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class WidgetCellRenderer extends CellRenderer implements UIParent, Titleable
{
    // {{{ public properties

    /**
     * Identifier of this widget cell renderer
     *
     * Identifier must be unique. This property is required and cannot be a
     * data-mapped value.
     *
     * @var string
     */
    public $id;

    /**
     * Unique value used to uniquely identify the replicated widget.
     * If null, no replicating is done and the prototype widget is used.
     */
    public $replicator_id = null;

    // }}}
    // {{{ private properties

    /**
     * A reference to the prototype widget for this cell renderer
     *
     * @var Widget
     */
    private $prototype_widget = null;

    private $mappings = array();
    private $clones = array();
    private $widgets = array();
    private $property_values = array();

    /**
     * Whether or not renderings of this cell renderer are using a dynamic
     * {@link WidgetCellRenderer::$replicator_id} to create cloned widgets
     *
     * @var boolean
     *
     * @see WidgetCellRenderer::render()
     */
    private $using_clone_replication = false;

    /**
     * Whether or not renderings of this cell renderer are using a null
     * {@link WidgetCellRenderer::$replicator_id} and rendering the
     * prototype widget instead of cloned widgets
     *
     * @var boolean
     *
     * @see WidgetCellRenderer::render()
     */
    private $using_null_replication = false;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new widget cell renderer
     */
    public function __construct()
    {
        parent::__construct();

        $this->makePropertyStatic('id');

        // auto-generate an id to use if no id is set
        $this->id = $this->getUniqueId();
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Fulfills UIParent::addChild()
     *
     * @throws Exception\Exception
     */
    public function addChild(Object $child)
    {
        if ($this->prototype_widget === null) {
            $this->setPrototypeWidget($child);
        } else {
            throw new Exception\Exception(
                'Can only add one widget to a widget cell renderer'
            );
        }
    }

    // }}}
    // {{{ public function getPropertyNameToMap()

    public function getPropertyNameToMap(Object $object, $name)
    {
        if ($this === $object)
            return $name;

        $mangled_name = $name;
        $suffix = 0;

        if (property_exists($this, $name)) {
            $mangled_name = $name.$suffix;
            $suffix++;
        }

        while (array_key_exists($mangled_name, $this->mappings)) {
            $mangled_name = $name.$suffix;
            $suffix++;
        }

        $this->mappings[$mangled_name] =
            array('object' => $object, 'property' => $name);

        return $mangled_name;
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this cell renderer
     *
     * This calls {@link Widget::init()} on this renderer's widget.
     */
    public function init()
    {
        $replicators = null;

        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName());

            if ($replicators !== null) {
                foreach ($replicators as $replicator) {
                    $this->createClonedWidget($replicator);
                }
            }
        }

        if ($replicators === null)
            $this->prototype_widget->init();
    }

    // }}}
    // {{{ public function process()

    public function process()
    {
        $form = $this->getForm();
        if ($form === null) {
            $replicators = null;
        } else {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName());
        }

        if ($replicators === null) {
            if ($this->prototype_widget !== null &&
                !$this->prototype_widget->isProcessed()) {
                $this->prototype_widget->process();
            }
        } else {
            foreach ($replicators as $replicator) {
                $widget = $this->getClonedWidget($replicator);
                if (!$widget->isProcessed()) {
                    $widget->process();
                }
            }
        }
    }

    // }}}
    // {{{ public function render()

    /**
     * @throws Exception\Exception
     */
    public function render()
    {
        if (!$this->visible)
            return;

        parent::render();

        if ($this->replicator_id === null) {
            if ($this->using_clone_replication) {
                throw new Exception\Exception(
                    'Cannot mix null replicator_id values with non-null '.
                    'replicator_id values. Make sure this widget cell '.
                    'renderer\'s replicator_id is set before rendering '.
                    'this renderer.'
                );
            }

            if ($this->prototype_widget !== null) {
                $this->applyPropertyValuesToPrototypeWidget();
                $this->prototype_widget->display();
            }

            $this->using_null_replication = true;
        } else {
            if ($this->using_null_replication) {
                throw new Exception\Exception(
                    'Cannot mix non-null replicator_id values with null '.
                    'replicator_id values. All prior renderings of this '.
                    'widget cell renderer have had a null value for the '.
                    'replicator_id.'
                );
            }

            if ($this->prototype_widget->id === null) {
                throw new Exception\Exception(
                    'Prototype widget must have a non-null id.'
                );
            }

            $widget = $this->getClonedWidget($this->replicator_id);
            if ($widget === null)
                return;

            $form = $this->getForm();
            if ($form === null) {
                throw new Exception\Exception(
                    'Cell renderer container must be inside a Form for '.
                    'WidgetCellRenderer to work.'
                );
            }

            $form->addHiddenField(
                $this->getReplicatorFieldName(),
                array_keys($this->clones));

            $this->applyPropertyValuesToClonedWidget($widget);
            $widget->display();

            $this->using_clone_replication = true;
        }
    }

    // }}}
    // {{{ public function setPrototypeWidget()

    /**
     *
     * @param Widget $widget
     */
    public function setPrototypeWidget(Widget $widget)
    {
        $this->prototype_widget = $widget;
        $widget->parent = $this;
    }

    // }}}
    // {{{ public function getPrototypeWidget()

    /**
     * Gets the prototype widget of this widget cell renderer
     *
     * @return Widget the prototype widget of this widget cell renderer.
     */
    public function getPrototypeWidget()
    {
        return $this->prototype_widget;
    }

    // }}}
    // {{{ public function getWidget()

    /**
     * Retrieves a reference to a replicated widget
     *
     * @param string $replicator_id the replicator id of the replicated widget.
     * @param string $widget_id     the unique id of the original widget, or
     *                              null to get the root.
     *
     * @return Widget a reference to the replicated widget, or null if the
     *                widget is not found.
     */
    public function getWidget($replicator_id, $widget_id = null)
    {
        $widget = null;

        if ($widget_id === null) {
            if (isset($this->widgets[$replicator_id])) {
                $widget = current($this->widgets[$replicator_id]);
            }
        } else {
            if (isset($this->widgets[$replicator_id][$widget_id])) {
                $widget = $this->widgets[$replicator_id][$widget_id];
            }
        }

        return $widget;
    }

    // }}}
    // {{{ public function getWidgets()

    /**
     * Gets an array of replicated widgets indexed by the replicator_id
     *
     * If this cell renderer's form is submitted, only cloned widgets that were
     * displayed and processed are returned.
     *
     * @param string $widget_id the unique id of the original widget, or null
     *                          to get the root.
     *
     * @return array an array of widgets indexed by replicator_id.
     *
     * @throws Exception\WidgetNotFoundException if a widget id is specified
     *         and no such widget exists in the subtree.
     */
    public function getWidgets($widget_id = null)
    {
        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName());

            $widgets = array();
            if (is_array($replicators)) {
                foreach ($this->clones as $replicator_id => $clone) {
                    if (in_array($replicator_id, $replicators)) {
                        if ($widget_id !== null &&
                            !isset($this->widgets[$replicator_id][$widget_id])) {
                            throw new Exception\WidgetNotFoundException(
                                sprintf(
                                    'No widget with the id "%s" exists in the '.
                                    'cloned widget sub-tree of this '.
                                    'WidgetCellRenderer.',
                                    $widget_id
                                ),
                                0,
                                $widget_id
                            );
                        }

                        $widgets[$replicator_id] =
                            $this->getWidget($replicator_id, $widget_id);
                    }
                }
            }
        } else {
            $widgets = $this->clones;
        }

        return $widgets;
    }

    // }}}
    // {{{ public function getDataSpecificCSSClassNames()

    /**
     * Gets the data specific CSS class names for this widget cell renderer
     *
     * If the widget within this cell renderer has messages, a CSS class of
     * 'swat-error' is added to the base CSS classes of this cell renderer.
     *
     * @return array the array of data specific CSS class names for this widget
     *               cell-renderer.
     */
    public function getDataSpecificCSSClassNames()
    {
        $classes = array();

        if ($this->replicator_id !== null &&
            $this->hasMessage($this->replicator_id)) {
            $classes[] = 'swat-error';
        }

        return $classes;
    }

    // }}}
    // {{{ public function getMessages()

    /**
     * Gathers all messages from the widget of this cell renderer for the given
     * replicator id
     *
     * @param mixed $replicator_id an optional replicator id of the row to
     *                             gather messages from. If no replicator id
     *                             is specified, the current replicator_id is
     *                             used.
     *
     * @return array an array of {@link Model\Message} objects.
     */
    public function getMessages($replicator_id = null)
    {
        $messages = array();

        if ($replicator_id !== null) {
            $messages = $this->getClonedWidget($replicator_id)->getMessages();
        } elseif ($this->replicator_id !== null) {
            $messages =
                $this->getClonedWidget($this->replicator_id)->getMessages();
        }

        return $messages;
    }

    // }}}
    // {{{ public function hasMessage()

    /**
     * Gets whether or not this widget cell renderer has messages
     *
     * @param mixed $replicator_id an optional replicator id of the row to
     *                             check for messages. If no replicator id is
     *                             specified, the current replicator_id is
     *                             used.
     *
     * @return boolean true if this widget cell renderer has one or more
     *                 messages for the given replicator id and false if it
     *                 does not.
     */
    public function hasMessage($replicator_id = null)
    {
        $has_message = false;

        if ($replicator_id !== null) {
            $has_message =
                $this->getClonedWidget($replicator_id)->hasMessage();
        } elseif ($this->replicator_id !== null) {
            $has_message =
                $this->getClonedWidget($this->replicator_id)->hasMessage();
        }

        return $has_message;
    }

    // }}}
    // {{{ public function getTitle()

    /**
     * Gets the title of this widget cell renderer
     *
     * The title is taken from this cell renderer's parent. Satisfies the
     * {@link Titleable::getTitle()} interface.
     *
     * @return string the title of this widget cell renderer.
     */
    public function getTitle()
    {
        $title = null;

        if (isset($this->parent->title))
            $title = $this->parent->title;

        return $title;
    }

    // }}}
    // {{{ public function getTitleContentType()

    /**
     * Gets the title content-type of this widget cell renderer
     *
     * Implements the {@link Titleable::getTitleContentType()} interface.
     *
     * @return string the title content-type of this widget cell renderer.
     */
    public function getTitleContentType()
    {
        $title_content_type = 'text/plain';

        if (isset($this->parent->title_content_type))
            $title_content_type = $this->parent->title_content_type;

        return $title_content_type;
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this widget cell renderer
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this widget
     *                          cell renderer.
     *
     * @see Object::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        if ($this->using_null_replication) {
            $set->addEntrySet(
                $this->getPrototypeWidget()->getHtmlHeadEntrySet());
        } else {
            $widgets = $this->getWidgets();
            foreach ($widgets as $widget) {
                $set->addEntrySet($widget->getHtmlHeadEntrySet());
            }
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this widget
     * cell renderer
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed by
     *                          this widget cell renderer.
     *
     * @see Object::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        if ($this->using_null_replication) {
            $set->addEntrySet(
                $this->getPrototypeWidget()->getAvailableHtmlHeadEntrySet());
        } else {
            $widgets = $this->getWidgets();
            foreach ($widgets as $widget) {
                $set->addEntrySet($widget->getAvailableHtmlHeadEntrySet());
            }
        }

        return $set;
    }

    // }}}
    // {{{ public function getDescendants()

    /**
     * Gets descendant UI-objects
     *
     * The descendant UI-objects of a widget cell renderer are cloned widgets,
     * not the prototype widget.
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this widget cell renderer. If
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

        foreach ($this->getWidgets() as $cloned_widget) {
            if ($class_name === null || $cloned_widget instanceof $class_name) {
                if ($cloned_widget->id === null) {
                    $out[] = $cloned_widget;
                } else {
                    $out[$cloned_widget->id] = $cloned_widget;
                }
            }

            if ($cloned_widget instanceof UIParent) {
                $out = array_merge(
                    $out,
                    $cloned_widget->getDescendants($class_name)
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
     * The descendant UI-objects of a widget cell renderer are cloned widgets,
     * not the prototype widget.
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

        foreach ($this->getWidgets() as $cloned_widget) {
            if ($cloned_widget instanceof $class_name) {
                $out = $cloned_widget;
                break;
            }

            if ($cloned_widget instanceof UIParent) {
                $out = $cloned_widget->getFirstDescendant($class_name);
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
     * subtree below this widget cell renderer.
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
     * widget cell renderer.
     *
     * @param array $states an array of UI-object states with UI-object
     *                      identifiers as array keys.
     */
    public function setDescendantStates(array $states)
    {
        $state = '\Silverorange\Swat\Model\State';
        foreach ($this->getDescendants($state) as $id => $object) {
            if (isset($states[$id]))
                $object->setState($states[$id]);
        }
    }

    // }}}
    // {{{ public function __set()

    /**
     * Maps a data field to a property of a widget in the widget tree
     *
     * @todo document me better
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->mappings)) {
            $this->property_values[$name] = $value;
        } else {
            // TODO: throw something meaningful
            throw new Exception\Exception();
        }
    }

    // }}}
    // {{{ protected function getReplicatorFieldName()

    protected function getReplicatorFieldName()
    {
        $name = $this->id.'_replicators';

        $widget = $this->getFirstAncestor('\Silverorange\Swat\UI\Widget');
        if ($widget->id) {
            $name = $widget->id.'_'.$name;
        }

        return $name;
    }

    // }}}
    // {{{ private function getForm()

    /**
     * Gets the form
     *
     * @return Form the form this cell renderer's view is contained in.
     */
    private function getForm()
    {
        $form = $this->getFirstAncestor('\Silverorange\Swat\UI\Form');
        return $form;
    }

    // }}}
    // {{{ private function getClonedWidget()

    private function getClonedWidget($replicator)
    {
        if (!isset($this->clones[$replicator]))
            $this->createClonedWidget($replicator);

        return $this->clones[$replicator];
    }

    // }}}
    // {{{ private function applyPropertyValuesToPrototypeWidget()

    private function applyPropertyValuesToPrototypeWidget()
    {
        foreach ($this->property_values as $name => $value) {
            $object = $this->mappings[$name]['object'];
            $property = $this->mappings[$name]['property'];
            $object->$property = $value;
        }
    }

    // }}}
    // {{{ private function applyPropertyValuesToClonedWidget()

    private function applyPropertyValuesToClonedWidget($cloned_widget)
    {
        foreach ($this->property_values as $name => $value) {
            $object = $this->mappings[$name]['object'];
            $property = $this->mappings[$name]['property'];

            $prototype_descendants = array($this->prototype_widget);
            $cloned_descendants = array($cloned_widget);

            if ($this->prototype_widget instanceof UIParent) {
                $prototype_descendants = array_merge($prototype_descendants,
                    $this->prototype_widget->getDescendants());

                $cloned_descendants = array_merge($cloned_descendants,
                    $cloned_widget->getDescendants());
            }

            $cloned_object = null;
            foreach ($prototype_descendants as $index => $prototype_object) {
                if ($object === $prototype_object) {
                    $cloned_object =
                        $this->widgets[$this->replicator_id][$object->id];

                    break;
                }
            }

            if ($cloned_object === null) {
                throw new Exception\Exception(
                    'Cloned widget tree does not match prototype widget tree.'
                );
            }

            if ($cloned_object->$property instanceof CellRendererMapping)
                $cloned_object->$property = $value;
        }
    }

    // }}}
    // {{{ private function createClonedWidget()

    private function createClonedWidget($replicator)
    {
        if ($this->prototype_widget === null)
            return;

        $suffix = '_'.$this->id.'_'.$replicator;
        $new_widget = $this->prototype_widget->copy($suffix);
        $new_widget->parent = $this;
        $this->widgets[$replicator] = array();

        if ($new_widget->id !== null) {
            // lookup array uses original ids
            $old_id = substr($new_widget->id, 0, -strlen($suffix));
            $this->widgets[$replicator][$old_id] = $new_widget;
        }

        if ($new_widget instanceof UIParent) {
            foreach ($new_widget->getDescendants() as $descendant) {
                if ($descendant->id !== null) {
                    // lookup array uses original ids
                    $old_id = substr($descendant->id, 0, -strlen($suffix));
                    $this->widgets[$replicator][$old_id] = $descendant;
                }
            }
        }

        $new_widget->init();

        $this->clones[$replicator] = $new_widget;
    }

    // }}}
}
