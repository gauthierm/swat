<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Html;

/**
 * Stores and outputs an HTML head entry for a stylesheet include
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class StyleSheetResource extends Resource
{
    // {{{ public function display()

    public function display($uri_prefix = '', $tag = null)
    {
        $uri = $this->uri;

        // append tag if it is set
        if ($tag !== null) {
            $uri = (strpos($uri, '?') === false ) ?
                $uri.'?'.$tag :
                $uri.'&'.$tag;
        }

        printf('<link rel="stylesheet" type="text/css" href="%s%s" />',
            $uri_prefix,
            $uri);
    }

    // }}}
    // {{{ public function displayInline()

    public function displayInline($path)
    {
        echo '<style type="text/css" media="all">';
        readfile($path.$this->getUri());
        echo '</style>';
    }

    // }}}
}

?>
