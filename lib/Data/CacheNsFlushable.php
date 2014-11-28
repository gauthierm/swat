<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Data;

/**
 * Interface that supports flushing cache name-spaces
 *
 * @package   SwatDB
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface CacheNsFlushable
{
    // {{{ public function flushNs()

    /**
     * Flushes a cache name-space
     *
     * @param string $ns the name-space to flush.
     */
    public function flushNs($ns);

    // }}}
}

?>
