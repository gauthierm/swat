<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/exceptions/SwatException.php';

/**
 * Thrown when character data is in an unrecognized or invalid character
 * encoding
 *
 * The official character set of Swat is UTF-8.
 *
 * @package   Swat
 * @copyright 2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidCharacterEncodingException extends SwatException
{
}

?>
