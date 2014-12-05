<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Util;

/**
 * A block of content in the widget tree
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ContentBlock extends Control
{
    // {{{ public properties

    /**
     * User visible textual content of this widget
     *
     * @var string
     */
    public $content = '';

    /**
     * Optional content type
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    // }}}
    // {{{ public function display()

    /**
     * Displays this content
     *
     * Merely performs an echo of the content.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        if ($this->content_type === 'text/plain') {
            echo Util\String::minimizeEntities($this->content);
        } else {
            echo $this->content;
        }
    }

    // }}}
}
