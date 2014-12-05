<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Html;

/**
 * Stores and outputs an HTML head entry
 *
 * Head entries are things like scripts and styles that belong in the HTML
 * head section.
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class Resource
{
    // {{{ protected properties

    /**
     * The uri of this head entry
     *
     * @var string
     */
    protected $uri = '';

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new HTML head entry
     *
     * @param string  $uri the uri of the entry.
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    // }}}
    // {{{ abstract public function display()

    /**
     * Displays this html head entry
     *
     * Entries are displayed differently based on type.
     *
     * @param string $uri_prefix an optional string to prefix the URI with.
     * @param string $tag        an optional tag to suffix the URI with. This
     *                           is suffixed as a HTTP get var and can be used
     *                           to explicitly refresh the browser cache.
     */
    abstract public function display($uri_prefix = '', $tag = null);

    // }}}
    // {{{ abstract public function displayInline()

    /**
     * Displays the resource referenced by this html head entry inline
     *
     * Entries are displayed differently based on type.
     *
     * @param string $path the path containing the resource files.
     */
    abstract public function displayInline($path);

    // }}}
    // {{{ public function getUri()

    /**
     * Gets the URI of this HTML head entry
     *
     * @return string the URI of this HTML head entry.
     */
    public function getUri()
    {
        return $this->uri;
    }

    // }}}
    // {{{ public function getType()

    /**
     * Gets the type of this HTML head entry
     *
     * @return string the type of this HTML head entry.
     */
    public function getType()
    {
        return get_class($this);
    }

    // }}}
}
