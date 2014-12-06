<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Model;

use Silverorange\Swat\L;

/**
 * A traversable list contining options for yes and no
 *
 * This can be added to any UI\OptionControl.
 *
 * @package   Swat
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       UI\OptionControl::addOptionsArray()
 */
class YesNoOptionList implements \IteratorAggregate
{
    // {{{ constants

    const NO = false;
    const YES = true;

    // }}}
    // {{{ protected properties

    /**
     * @var array
     */
    protected $options = array();

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new yes/no option list
     */
    public function __construct()
    {
        $this->options = array(
            new Option(self::NO, L::_('No'),
            new Option(self::YES, L::_('Yes'),
        );
    }

    // }}}
    // {{{ public function getIterator()

    /**
     * Gets an iterator over the options of this list
     *
     * @return \Traversable an iterator over the options of this list.
     */
    public function getIterator()
    {
        return $this->options;
    }

    // }}}
}
