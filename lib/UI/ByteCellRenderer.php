<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Util;

/**
 * A cell renderer for rendering base-2 units of information
 *
 * This cell renderer should be used for displaying things such as file and
 * memory sizes.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ByteCellRenderer extends CellRenderer
{
    // {{{ public properties

    /**
     * Value in bytes
     *
     * @var float
     */
    public $value;

    // }}}
    // {{{ public function render()

    /**
     * Renders the contents of this cell
     *
     * @see CellRenderer::render()
     */
    public function render()
    {
        if (!$this->visible)
            return;

        parent::render();

        echo Util\String::minimizeEntities(
            Util\String::byteFormat($this->value)
        );
    }

    // }}}
}
