<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\L;

/**
 * A radio list selection widget for a Yes/No option.
 *
 * @package   Swat
 * @copyright 2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class YesNoRadioList extends RadioList
{
    // {{{ constants

    const NO  = false;
    const YES = true;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new yes/no radio list
     *
     * Sets the options of this radio list to be yes and no.
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->addOption(self::NO, L::_('No'));
        $this->addOption(self::YES, L::_('Yes'));
    }

    // }}}
}
