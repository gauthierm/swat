<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

/**
 * A replicable container that replicates {@link NoteBookChild} objects
 *
 * @package   Swat
 * @copyright 2007-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ReplicableNoteBookChild extends ReplicableContainer
    implements NoteBookChild
{
    // {{{ public function getPages()

    /**
     * Gets the notebook pages of this replicable notebook child
     *
     * Implements the {@link NoteBookChild::getPages()} interface.
     *
     * @return array an array containing all the replicated pages of this
     *               child.
     */
    public function getPages()
    {
        $pages = array();

        foreach ($this->children as $child) {
            if ($child instanceof NoteBookChild) {
                $pages = array_merge($pages, $child->getPages());
            }
        }

        return $pages;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a {@link NoteBookChild} to this replicable notebook child
     *
     * This method fulfills the {@link UIParent} interface.
     *
     * @param NoteBookChild $child the notebook child to add.
     *
     * @throws Exception\InvalidClassException if the given object is not an
     *         instance of NoteBookChild.
     *
     * @see UIParent
     */
    public function addChild(Object $child)
    {
        if (!$child instanceof NoteBookChild) {
            throw new Exception\InvalidClassException(
                'Only NoteBookChild objects may be nested within a '.
                'ReplicableNoteBookChild object.',
                0,
                $child
            );
        }

        parent::addChild($child);
    }

    // }}}
}

?>
