<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Interface that supports setting a flushable cache
 *
 * @package   SwatDB
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBFlushable
{
    // {{{ public function setFlushableCache()

    /**
     * Sets the flushable cache to use for this object
     *
     * @param SwatDBCacheNsFlushable $cache the flushable cache to use for
     *                                      this object.
     * @see SwatDBCacheNsFlushable
     */
    public function setFlushableCache(SwatDBCacheNsFlushable $cache);

    // }}}
}

?>
