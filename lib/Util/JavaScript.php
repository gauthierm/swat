<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Util;

/**
 * JavaScript utilities
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class JavaScript
{
    // {{{ public static function displayInlineJavaScript()

    /**
     * Displays inline JavaScript properly encapsulating the script in a CDATA
     * section
     *
     * @param string $javascript the inline JavaScript to display.
     */
    public static function displayInline($javascript)
    {
        if ($javascript != '') {
            echo '<script type="text/javascript">',
                "\n//<![CDATA[\n",
                rtrim($javascript),
                "\n//]]>\n",
                "</script>";
        }
    }

    // }}}
    // {{{ private function __construct()

    private function __construct()
    {
    }

    // }}}
}

?>
