<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Html;

/**
 * Stores and outputs an HTML head entry for a JavaScript include
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class JavaScriptResource extends Resource
{
    // {{{ public function display()

    public function display($uri_prefix = '', $tag = null)
    {
        $uri = $this->uri;

        // append tag if it is set
        if ($tag !== null) {
            $uri = (strpos($uri, '?') === false)
                ? $uri . '?' . $tag
                : $uri . '&' . $tag;
        }

        printf(
            '<script type="text/javascript" src="%s%s"></script>',
            $uri_prefix,
            $uri
        );
    }

    // }}}
    // {{{ public function displayInline()

    public function displayInline($path)
    {
        echo '<script type="text/javascript">';
        readfile($path . $this->getUri());
        echo '</script>';
    }

    // }}}
}
