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
    // {{{ public static function quoteString()

    /**
     * Safely quotes a PHP string into a JavaScript string
     *
     * Strings are always quoted using single quotes. The characters documented
     * at {@link http://code.google.com/p/doctype/wiki/ArticleXSSInJavaScript}
     * are escaped to prevent XSS attacks.
     *
     * @param string $string the PHP string to quote as a JavaScript string.
     *
     * @return string the quoted JavaScript string. The quoted string is
     *                wrapped in single quotation marks and is safe to display
     *                in inline JavaScript.
     */
    public static function quoteString($string)
    {
        $search = array(
            '\\',           // backslash quote
            '&',            // ampersand
            '<',            // less than
            '>',            // greater than
            '=',            // equal
            '"',            // double quote
            "'",            // single quote
            "\t",           // tab
            "\r\n",         // line ending (Windows)
            "\r",           // carriage return
            "\n",           // line feed
            "\xc2\x85",     // next line
            "\xe2\x80\xa8", // line separator
            "\xe2\x80\xa9", // paragraph separator
        );

        $replace = array(
            '\\\\',   // backslash quote
            '\x26',   // ampersand
            '\x3c',   // less than
            '\x3e',   // greater than
            '\x3d',   // equal
            '\x22',   // double quote
            '\x27',   // single quote
            '\t',     // tab
            '\n',     // line ending (Windows, transformed to line feed)
            '\n',     // carriage return (transformed to line feed)
            '\n',     // line feed
            '\u0085', // next line
            '\u2028', // line separator
            '\u2029', // paragraph separator
        );

        // escape XSS vectors
        $string = str_replace($search, $replace, $string);

        // quote string
        $string = "'".$string."'";

        return $string;
    }

    // }}}
    // {{{ private function __construct()

    /**
     * Don't allow instantiation of this object
     *
     * This class contains only static methods and should not be instantiated.
     */
    private function __construct()
    {
    }

    // }}}
}
