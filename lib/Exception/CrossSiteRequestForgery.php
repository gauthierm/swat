<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

use Silverorange\Swat\UI;

/**
 * Thrown by {@link UI\Form} when a possible cross-site request forgery is
 * detected
 *
 * By design, it is not possible to get the correct authentication token from
 * this exception. Since it is not possible to get the correct authentication
 * token, the incorrect token is not useful and is also not availble in this
 * exception.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class CrossSiteRequestForgeryException extends Exception
{
    // {{{ protected properties

    /**
     * The form that did not authenticate
     *
     * @var UI\Form
     */
    protected $form = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new cross-site request forgery exception
     *
     * @param string  $message the message of the exception.
     * @param integer $code    the code of the exception.
     * @param UI\Form $form    the form that did not authenticate.
     */
    public function __construct($message, $code, UI\Form $form)
    {
        parent::__construct($message, $code);
        $this->form = $form;
    }

    // }}}
    // {{{ public function getForm()

    /**
     * Gets the form that did not authenticate
     *
     * @return UI\Form the form that did not authenticate.
     */
    public function getForm()
    {
        return $this->form;
    }

    // }}}
}
