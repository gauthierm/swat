<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Html;

/**
 * Stores and outputs an HTML head entry for a LESS stylesheet include
 *
 * @package   Swat
 * @copyright 2012-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class LessResource extends StyleSheetResource
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

        printf('<link rel="stylesheet/less" type="text/css" href="%s%s" />',
            $uri_prefix,
            $uri);
    }

    // }}}
    // {{{ public function getStyleSheetResource()

    public function getStyleSheetResource()
    {
        return new StyleSheetResource($this->uri);
    }

    // }}}
}

?>
