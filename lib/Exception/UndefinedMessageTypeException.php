<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

/**
 * Thrown when a message type is used that is not defined
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class UndefinedMessageTypeException extends Exception
{
    // {{{ protected properties

    /**
     * The name of the message type that is undefined
     *
     * @var string
     */
    protected $message_type = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new undefined message type exception
     *
     * @param string  $message      the message of the exception.
     * @param integer $code         the code of the exception.
     * @param string  $message_type the name of the message type that is
     *                              undefined.
     */
    public function __construct(
        $message = null,
        $code = 0,
        $message_type = null
    ) {
        parent::__construct($message, $code);
        $this->message_type = $message_type;
    }

    // }}}
    // {{{ public function getMessageType()

    /**
     * Gets the name of the message type that is undefined
     *
     * @return string the name of the message type that is undefined.
     */
    public function getMessageType()
    {
        return $this->message_type;
    }

    // }}}
}
