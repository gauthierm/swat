<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * An phone number entry widget
 *
 * @package   Swat
 * @copyright 2010 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class PhoneEntry extends Entry
{
    // {{{ protected function getInputTag()

    /**
     * Get the input tag to display
     *
     * @return Html\Tag the input tag to display.
     */
    protected function getInputTag()
    {
        $tag = parent::getInputTag();
        $tag->type = 'tel';
        return $tag;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this entry
     *
     * @return array the array of CSS classes that are applied to this
     *               entry.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-phone-entry');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
