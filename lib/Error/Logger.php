<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Error;

/**
 * Abstract base class for logging error objects
 *
 * A custom error logger can be used to change how uncaught errors are logged
 * in an application. For example, you may want to log errors in a database
 * or store error details in a separate file.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Error::setLogger()
 */
abstract class Logger
{
    // {{{ public abstract function log()

    /**
     * Logs an error
     *
     * This is called by {@link Error::process()}.
     *
     * @param Error $e the error to log.
     */
    public abstract function log(Error $e);

    // }}}
}
