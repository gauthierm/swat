<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

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
 * @see       Exception::setDisplayer()
 */
abstract class Displayer
{
    // {{{ abstract public function display()

    /**
     * Displays a Swat exception
     *
     * This is called by {@link Exception::process()}.
     *
     * @param Exception $e the exception to display.
     */
    abstract public function display(Exception $e);

    // }}}
}
