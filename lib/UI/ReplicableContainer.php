<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A container that replicates itself and its children
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ReplicableContainer extends DisplayableContainer implements Replicable
{
    // {{{ public properties

    /**
     * An array of unique ids, one for each replication
     *
     * The ids are used to suffix the original widget ids to create unique
     * ids for the replicated widgets.
     *
     * @var array
     */
    public $replication_ids = null;

    // }}}
    // {{{ private properties

    private $widgets = array();
    private $prototype_widgets = array();

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new replicator container
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->requires_id = true;
    }

    // }}}
    // {{{ public function init()

    /**
     * Initilizes this replicable container
     *
     * Goes through the internal widgets, clones them, and adds them to the
     * widget tree.
     */
    public function init()
    {
        if ($this->replication_ids === null)
            $this->replication_ids = array(0);

        // Remove children, these are now the prototype widgets
        foreach ($this->children as $child_widget)
            $this->prototype_widgets[] = $this->remove($child_widget);

        foreach ($this->replication_ids as $id)
            $this->addReplication($id);

        parent::init();
    }

    // }}}
    // {{{ public function addReplication()

    public function addReplication($id)
    {
        if (!in_array($id, $this->replication_ids))
            $this->replication_ids[] = $id;

        $suffix = '_' . $id;

        foreach ($this->prototype_widgets as $prototype_widget) {
            $widget = $prototype_widget->copy($suffix);

            if ($widget->id !== null)
                $this->widgets[$id][$prototype_widget->id] = $widget;

            if ($widget instanceof UIParent) {
                foreach ($widget->getDescendants() as $descendant) {
                    if ($descendant->id !== null) {
                        $old_id = substr($descendant->id, 0, -strlen($suffix));
                        $this->widgets[$id][$old_id] = $descendant;
                    }
                }
            }

            $this->add($widget);
        }
    }

    // }}}
    // {{{ public function getWidget()

    /**
     * Retrives a reference to a replicated widget
     *
     * @param string $widget_id     the unique id of the original widget.
     * @param string $replicator_id the replicator id of the replicated widget.
     *
     * @return Widget a reference to the replicated widget, or null if the
     *                widget is not found.
     */
    public function getWidget($widget_id, $replicator_id)
    {
        $widget = null;

        if (isset($this->widgets[$replicator_id][$widget_id]))
            $widget = $this->widgets[$replicator_id][$widget_id];

        return $widget;
    }

    // }}}
}
