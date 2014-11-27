<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/SwatCheckboxCellRenderer.php';
require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatTableViewCheckAllRow.php';

/**
 * A special table-view column designed to contain a checkbox cell renderer
 *
 * A checkbox column adds a check-all row to the parent view. The check all
 * widget is used for controlling checkbox cell renderers. If your table-view
 * does not need check-all functionality a regular table-view column will
 * suffice.
 *
 * Checkbox columns must contain at least one {@link SwatCheckboxCellRenderer}.
 * If this column contains more than one checkbox cell renderer, the check-all
 * widget only applies to the first checkbox renderer.
 *
 * @package   Swat
 * @copyright 2005-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewCheckboxColumn extends SwatTableViewColumn
{
    // {{{ public properties

    /**
     * Whether to show a check-all row for this checkbox column
     *
     * This property only has an effect if a {@link SwatCheckboxCellRenderer}
     * is present inside this column.
     *
     * If a check-all row is never needed, use a regular
     * {@link SwatTableViewColumn} instead of a checkbox column.
     *
     * @var boolean
     */
    public $show_check_all = true;

    /**
     * Optional label title for the check-all widget
     *
     * Defaults to "Check All".
     *
     * @var string
     */
    public $check_all_title;

    /**
     * Optional content type for check-all widget title
     *
     * Defaults to text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $check_all_content_type = 'text/plain';

    /**
     * Count for displaying an extended-all checkbox
     *
     * When the check-all checkbox has been checked, an additional
     * checkbox will appear allowing the user to specify that they wish to
     * select all possible items. This is useful in cases where pagination
     * makes selecting all possible items impossible.
     *
     * @var integer
     */
    public $check_all_extended_count = 0;

    /**
     * Count for all visible items when displaying an extended-all checkbox
     *
     * @var integer
     */
    public $check_all_visible_count = 0;

    /**
     * Optional extended-all checkbox unit.
     *
     * Used for displaying a "check-all" message. Defaults to "items".
     */
    public $check_all_unit;

    // }}}
    // {{{ private properties

    /**
     * Check-all row added by this column to the parent table-view
     *
     * @var SwatTableViewCheckAllRow
     *
     * @see SwatTableViewCheckboxColumn::$show_check_all
     */
    private $check_all;

    // }}}
    // {{{ public function init()

    /**
     * Initializes this checkbox column
     */
    public function init()
    {
        parent::init();
        $this->createEmbeddedWidgets();

        $this->check_all->init();

        if ($this->show_check_all && $this->visible) {
            $this->parent->appendRow($this->check_all);
        }
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this checkbox column
     *
     * @see SwatView::getSelection()
     */
    public function process()
    {
        parent::process();

        if ($this->show_check_all)
            $this->check_all->process();
    }

    // }}}
    // {{{ public function isExtendedCheckAllSelected()

    /**
     * Whether or not the extended-check-all check-box was checked
     *
     * @return boolean whether or not the extended-checkbox was checked.
     */
    public function isExtendedCheckAllSelected()
    {
        return $this->check_all->isExtendedSelected();
    }

    // }}}
    // {{{ public function displayHeader()

    /**
     * Displays the contents of the header cell for this column
     */
    public function displayHeader()
    {
        if ($this->check_all_title !== null) {
            $this->check_all->title = $this->check_all_title;
            $this->check_all->content_type = $this->check_all_content_type;
        }

        $this->check_all->extended_count = $this->check_all_extended_count;
        $this->check_all->visible_count = $this->check_all_visible_count;
        $this->check_all->unit = $this->check_all_unit;

        parent::displayHeader();
    }

    // }}}
    // {{{ public function getCheckboxRendererId()

    /**
     * Gets the identifier of the first checkbox cell renderer in this column
     *
     * @return string the indentifier of the first checkbox cell renderer in
     *                this column.
     */
    private function getCheckboxRendererId()
    {
        return $this->getCheckboxRenderer()->id;
    }

    // }}}
    // {{{ public function getCheckboxRenderer()

    private function getCheckboxRenderer()
    {
        foreach ($this->getRenderers() as $renderer)
            if ($renderer instanceof SwatCheckboxCellRenderer)
                return $renderer;

        throw new SwatException("The checkbox column ‘{$this->id}’ must ".
            'contain a checkbox cell renderer.');
    }

    // }}}
    // {{{ public function extendedCheckAllSelected()

    /**
     * Whether or not the extended-check-all check-box was checked
     *
     * @return boolean whether or not the extended-checkbox was checked.
     */
    public function extendedCheckAllSelected()
    {
        return $this->check_all->extendedSelected();
    }

    // }}}
    // {{{ private function createEmbeddedWidgets()

    private function createEmbeddedWidgets()
    {
        $renderer_id = $this->getCheckboxRendererId();
        $this->check_all = new SwatTableViewCheckAllRow($this, $renderer_id);
    }

    // }}}
}

?>
