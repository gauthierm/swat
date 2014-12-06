<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * Interface for view selectors
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Model\ViewSelection
 * @see       View
 */
interface ViewSelector
{
    // {{{ public function getId()

    /**
     * Gets the identifier of this selector
     *
     * @return string the identifier of this selector.
     */
    public function getId();

    // }}}
}
