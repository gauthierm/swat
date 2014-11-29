<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Error;

/**
 * Abstract base class for displaying SwatError objects
 *
 * A custom error displayer can be used to change how uncaught errors are
 * displayed in an application. For example, you may want to display errors
 * in a separate file or display them using different XHTML markup.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Error::setDisplayer()
 */
abstract class Displayer
{
    // {{{ public abstract function display()

    /**
     * Displays an error
     *
     * This is called by {@link Error::process()}.
     *
     * @param Error $e the error to display.
     */
    public abstract function display(Error $e);

    // }}}
}

?>
