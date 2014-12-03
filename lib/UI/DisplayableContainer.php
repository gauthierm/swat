<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;

/**
 * Base class for containers that display an XHTML element
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DisplayableContainer extends Container
{
    // {{{ public function display()

    /**
     * Displays this container
     */
    public function display()
    {
        if (!$this->visible)
            return;

        Widget::display();

        $div = new Html\Tag('div');
        $div->id = $this->id;
        $div->class = $this->getCSSClassString();

        $div->open();
        $this->displayChildren();
        $div->close();
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this displayable
     * container
     *
     * @return array the array of CSS classes that are applied to this
     *               displayable container.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-displayable-container');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
