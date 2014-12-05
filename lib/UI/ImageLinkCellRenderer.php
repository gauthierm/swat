<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;

/**
 * A renderer that displays a hyperlinked image
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ImageLinkCellRenderer extends ImageCellRenderer
{
    // {{{ public properties

    /**
     * The href attribute in the XHTML anchor tag
     *
     * Optionally uses vsprintf() syntax, for example:
     * <code>
     * $renderer->link = 'MySection/MyPage/%s?id=%s';
     * </code>
     *
     * @var string
     *
     * @see LinkCellRenderer::$link_value
     */
    public $link;

    /**
     * A value or array of values to substitute into the link of this cell
     *
     * The value property may be specified either as an array of values or as
     * a single value. If an array is passed, a call to vsprintf() is done
     * on the {@link ImageLinkCellRenderer::$link} property. If the value is a
     * string a single sprintf() call is made.
     *
     * @var mixed
     *
     * @see ImageLinkCellRenderer::$link
     */
    public $link_value = null;

    // }}}
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

        if ($this->sensitive) {
            $anchor = new Html\Tag('a');

            if ($this->link_value === null) {
                $anchor->href = $this->link;
            } elseif (is_array($this->link_value)) {
                $anchor->href = vsprintf($this->link, $this->link_value);
            } else {
                $anchor->href = sprintf($this->link, $this->link_value);
            }

            $anchor->open();
        }

        parent::render();

        if ($this->sensitive) {
            $anchor->close();
        }
    }

    // }}}
}
