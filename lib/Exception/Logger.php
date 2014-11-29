<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

/**
 * Abstract base class for logging Swat exception objects
 *
 * A custom exception logger can be used to change how uncaught exceptions
 * are logged in an application. For example, you may want to log exceptions in
 * a database or store exception details in a separate file.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Exception::setLogger()
 */
abstract class Logger
{
    // {{{ public abstract function log()

    /**
     * Logs a Swat exception
     *
     * This is called by {@link Exception::process()}.
     *
     * @param Exception $e the exception to log.
     */
    public abstract function log(Exception $e);

    // }}}
}

?>
