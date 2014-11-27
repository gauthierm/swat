<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/exceptions/SwatException.php';

/**
 * Abstract base class for displaying SwatException objects
 *
 * A custom exception displayer can be used to change how uncaught exceptions
 * are displayed in an application. For example, you may want to display
 * exceptions in a separate file or display them using different XHTML
 * markup.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatException::setDisplayer()
 */
abstract class SwatExceptionDisplayer
{
    // {{{ public abstract function display()

    /**
     * Displays a SwatException
     *
     * This is called by SwatException::process().
     */
    public abstract function display(SwatException $e);

    // }}}
}

?>
