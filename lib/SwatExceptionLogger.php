<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/exceptions/SwatException.php';

/**
 * Abstract base class for logging SwatException objects
 *
 * A custom exception logger can be used to change how uncaught exceptions
 * are logged in an application. For example, you may want to log exceptions in
 * a database or store exception details in a separate file.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatException::setLogger()
 */
abstract class SwatExceptionLogger
{
    // {{{ public abstract function log()

    /**
     * Logs a SwatException
     *
     * This is called by SwatException::process().
     */
    public abstract function log(SwatException $e);

    // }}}
}

?>
