<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A percentage cell renderer
 *
 * @package   Swat
 * @copyright 2006-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class PercentageCellRenderer extends NumericCellRenderer
{
    // {{{ public function render()

    /**
     * Renders the contents of this cell
     *
     * @see CellRenderer::render()
     */
    public function render()
    {
        if (!$this->visible) {
            return;
        }

        CellRenderer::render();

        if ($this->value === null && $this->null_display_value !== null) {
            $this->renderNullValue();
        } else {
            $old_value = $this->value;
            $this->value = $this->value * 100;
            printf('%s%%', $this->getDisplayValue());
            $this->value = $old_value;
        }
    }

    // }}}
}
