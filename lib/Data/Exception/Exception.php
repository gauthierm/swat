<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Data\Exception;

use Silverorange\Swat\Exception as SwatException;

/**
 * A SwatDB Exception.
 *
 * @package   SwatDB
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Exception extends SwatException\Exception
{
    // {{{ private function __construct()

    public function __construct($message = null, $code = 0)
    {
        if (is_object($message) && ($message instanceof \PEAR_Error)) {
            $error = $message;
            $message = $error->getMessage();
            $message .= "\n".$error->getUserInfo();
            $code = $error->getCode();
        }

        parent::__construct($message, $code);
    }

    // }}}
}

?>
