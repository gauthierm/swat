<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Exception;

/**
 * Thrown when a stock type is used that is not defined
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class UndefinedStockTypeException extends Exception
{
    // {{{ protected properties

    /**
     * The name of the stock type that is undefined
     *
     * @var string
     */
    protected $stock_type = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new undefined stock type exception
     *
     * @param string  $message    the message of the exception.
     * @param integer $code       the code of the exception.
     * @param string  $stock_type the name of the stock type that is undefined.
     */
    public function __construct($message = null, $code = 0,
        $stock_type= null)
    {
        parent::__construct($message, $code);
        $this->stock_type = $stock_type;
    }

    // }}}
    // {{{ public function getStockType()

    /**
     * Gets the name of the stock type that is undefined
     *
     * @return string the name of the stock type that is undefined.
     */
    public function getStockType()
    {
        return $this->stock_type;
    }

    // }}}
}

?>
