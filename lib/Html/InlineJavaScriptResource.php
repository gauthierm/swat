<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Html;

use Silverorange\Swat\Util;

/**
 * @package   Swat
 * @copyright 2012-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InlineJavaScriptResource extends Resource
{
    // {{{ protected properties

    /**
     * @var string
     */
    protected $script;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new HTML head entry
     *
     * @param string  $script the script of this entry.
     */
    public function __construct($script)
    {
        parent::__construct(md5($script));
        $this->script = $script;
    }

    // }}}
    // {{{ public function display()

    public function display($uri_prefix = '', $tag = null)
    {
        Util\JavaScript::displayInline($this->script);
    }

    // }}}
    // {{{ public function displayInline()

    public function displayInline($path)
    {
        $this->display();
    }

    // }}}
}
