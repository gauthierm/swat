<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\L;

/**
 * A flydown (aka combo-box) selection widget for a Yes/No option.
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class YesNoFlydown extends Flydown
{
    // {{{ constants

    const NO  = false;
    const YES = true;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new yes/no flydown
     *
     * Sets the options of this flydown to be yes and no.
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->addOption(self::NO,  L::_('No'));
        $this->addOption(self::YES, L::_('Yes'));
    }

    // }}}
}
