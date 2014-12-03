<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Data;

/**
 * Interface that supports setting a flushable cache
 *
 * @package   SwatDB
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface Flushable
{
    // {{{ public function setFlushableCache()

    /**
     * Sets the flushable cache to use for this object
     *
     * @param CacheNsFlushable $cache the flushable cache to use for this
     *                                object.
     * @see CacheNsFlushable
     */
    public function setFlushableCache(CacheNsFlushable $cache);

    // }}}
}
