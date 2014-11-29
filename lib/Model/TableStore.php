<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Model;

/**
 * A data structure that can be used with the UI\TableView
 *
 * A new table store is empty by default. Use the
 * {@link TableStore::add()} method to add rows to a table store.
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class TableStore implements TableModel
{
    // {{{ private properties

    /**
     * The indvidual rows for this data structure
     *
     * @var array
     */
    private $rows = array();

    /**
     * The current index of the iterator interface
     *
     * @var integer
     */
    private $current_index = 0;

    // }}}
    // {{{ public function count()

    /**
     * Gets the number of rows
     *
     * This satisfies the \Countable interface.
     *
     * @return integer the number of rows in this data structure.
     */
    public function count()
    {
        return count($this->rows);
    }

    // }}}
    // {{{ public function current()

    /**
     * Returns the current element
     *
     * @return \stdClass the current element.
     */
    public function current()
    {
        return $this->rows[$this->current_index];
    }

    // }}}
    // {{{ public function key()

    /**
     * Returns the key of the current element
     *
     * @return integer the key of the current element
     */
    public function key()
    {
        return $this->current_index;
    }

    // }}}
    // {{{ public function next()

    /**
     * Moves forward to the next element
     */
    public function next()
    {
        $this->current_index++;
    }

    // }}}
    // {{{ public function prev()

    /**
     * Moves forward to the previous element
     */
    public function prev()
    {
        $this->current_index--;
    }

    // }}}
    // {{{ public function rewind()

    /**
     * Rewinds this iterator to the first element
     */
    public function rewind()
    {
        $this->current_index = 0;
    }

    // }}}
    // {{{ public function valid()

    /**
     * Checks is there is a current element after calls to rewind() and next()
     *
     * @return boolean true if there is a current element and false if there
     *                 is not.
     */
    public function valid()
    {
        return array_key_exists($this->current_index, $this->rows);
    }

    // }}}
    // {{{ public function add()

    /**
     * Adds a row to this data structure
     *
     * @param \stdClass $data the data of the row to add.
     */
    public function add($data)
    {
        $this->rows[] = $data;
    }

    // }}}
    // {{{ public function addToStart()

    /**
     * Adds a row to the beginning of this data structure
     *
     * @param \stdClass $data the data of the row to add.
     */
    public function addToStart($data)
    {
        array_unshift($this->rows, $data);
        $this->current_index++;
    }

    // }}}
}

?>