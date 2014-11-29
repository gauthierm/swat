<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

/**
 * Thrown when a integer causes an arithmetic/buffer overflow
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class IntegerOverflowException extends \OverflowException
{
    // {{{ protected properties

    /**
     * Sign
     *
     * The sign of the integer, either positive or negative
     *
     * @var integer
     */
    protected $sign = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new invalid type exception
     *
     * @param string  $message the message of the exception.
     * @param integer $code    the code of the exception.
     * @param integer $sign    the sign of the integer, either positive or
     *                         negative.
     */
    public function __construct($message = null, $code = 0, $sign = 1)
    {
        parent::__construct($message, $code);

        $this->sign = $sign;
    }

    // }}}
    // {{{ public function getSign()

    /**
     * Gets the sign of the integer
     *
     * @return integer The sign of the integer, either positive or negative.
     */
    public function getSign()
    {
        return $this->sign;
    }

    // }}}
}

?>
