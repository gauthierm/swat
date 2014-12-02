<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A cell renderer that displays a message if it is asked to display
 * null text
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class NullTextCellRenderer extends TextCellRenderer
{
    // {{{ public properties

    /**
     * The text to display in this cell if the
     * {@link TextCellRenderer::$text} property is null when the render()
     * method is called
     *
     * @var string
     */
    public $null_text = '&lt;none&gt;';

    /**
     * Whether to test the {@link TextCellRenderer::$text} property for
     * null using strict equality.
     *
     * @var boolean
     */
    public $strict = false;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a null text cell renderer
     */
    public function __construct()
    {
        parent::__construct();

        $this->addStyleSheet(
            'packages/swat/styles/swat-null-text-cell-renderer.css'
        );
    }

    // }}}
    // {{{ public function render()

    /**
     * Renders this cell renderer
     */
    public function render()
    {
        if (!$this->visible)
            return;

        $is_null = ($this->strict) ?
            ($this->text === null) : ($this->text == null);

        if ($is_null) {
            $this->text = $this->null_text;

            echo '<span class="swat-null-text-cell-renderer">';
            parent::render();
            echo '</span>';

            // Reset the text so that subsequent $is_null checks pass.
            $this->text = null;
        } else {
            parent::render();
        }
    }

    // }}}
}

?>
