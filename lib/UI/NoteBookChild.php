<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A child of a {@link NoteBook}
 *
 * @package   Swat
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       NoteBook
 * @see       NoteBookPage
 */
interface NoteBookChild
{
    // {{{ public function getPages()

    /**
     * Gets the notebook pages of this child
     *
     * @return array an array of {@link NoteBookPage} objects.
     *
     * @see NoteBookPage
     */
    public function getPages();

    // }}}
}
