<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A Swat container that can replicate its contents
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface Replicable
{
    // {{{ public function getWidget()

    /**
     * Retrives a reference to a replicated widget
     *
     * @param string $widget_id     the unique id of the original widget.
     * @param string $replicator_id the replicator id of the replicated widget.
     *
     * @returns Widget a reference to the replicated widget.
     *
     * @throws Exception\WidgetNotFoundException
     */
    public function getWidget($widget_id, $replicator_id);

    // }}}
}

?>
