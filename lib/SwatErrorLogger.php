<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/SwatError.php';

/**
 * Abstract base class for logging SwatError objects
 *
 * A custom error logger can be used to change how uncaught errors are logged
 * in an application. For example, you may want to log errors in a database
 * or store error details in a separate file.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatError::setLogger()
 */
abstract class SwatErrorLogger
{
    // {{{ public abstract function log()

    /**
     * Logs a SwatError
     *
     * This is called by SwatError::process().
     */
    public abstract function log(SwatError $e);

    // }}}
}

?>
