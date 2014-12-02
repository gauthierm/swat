<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\L;

/**
 * This is a table view column that gets its own row.
 *
 * @package   Swat
 * @copyright 2005-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class TableViewSpanningColumn extends TableViewColumn
{
    // {{{ public properties

    /**
     * The number of columns to offset to the right
     *
     * @var integer
     */
    public $offset = 0;

    // }}}
    // {{{ public function hasVisibleRenderer()

    /**
     * Whether or not this row-column has one or more visible cell renderers
     *
     * @param mixed $row a data object containing the data for a single row
     *                   in the table store for this group. This object may
     *                   affect the visibility of renderers in this row-
     *                   column.
     *
     * @return boolean true if this row-column has one or more visible cell
     *                 renderers and false if it does not.
     */
    public function hasVisibleRenderer($row)
    {
        $this->setupRenderers($row);

        $visible_renderers = false;

        foreach ($this->renderers as $renderer) {
            if ($renderer->visible) {
                $visible_renderers = true;
                break;
            }
        }

        return $visible_renderers;
    }

    // }}}
    // {{{ protected function displayRenderers()

    /**
     * Renders each cell renderer in this column inside a wrapping XHTML
     * element
     *
     * @param mixed $data the data object being used to render the cell
     *                    renderers of this field.
     *
     * @throws Exception\Exception
     */
    protected function displayRenderers($row)
    {
        $offset = $this->offset;

        if ($this->title != '') {
            if ($offset == 0)
                $offset = 1;

            $th_tag = new Html\Tag('th', $this->getThAttributes());
            $th_tag->colspan = $offset;
            $th_tag->setContent(
                sprintf(
                    L::_('%s:'),
                    $this->title
                ),
                $this->title_content_type
            );
            $th_tag->display();
        } elseif ($offset > 0) {
            $td_tag = new Html\Tag('td');
            $td_tag->colspan = $offset;
            $td_tag->setContent('&nbsp;', 'text/xml');
            $td_tag->display();
        }

        $td_tag = new Html\Tag('td', $this->getTdAttributes());
        $td_tag->colspan =
            $this->view->getXhtmlColspan() - $offset;

        $td_tag->open();
        $this->displayRenderersInternal($row);
        $td_tag->close();
    }

    // }}}
}

?>
