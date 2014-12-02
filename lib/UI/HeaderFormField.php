<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class HeaderFormField extends FormField
{
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this header form field
     *
     * @return array the array of CSS classes that are applied to this header
     *               form field.
     */
    protected function getCSSClassNames()
    {
        $classes = parent::getCSSClassNames();
        array_unshift($classes, 'swat-header-form-field');
        return $classes;
    }

    // }}}
}

?>
