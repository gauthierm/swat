<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\Model;
use Silverorange\Swat\Util;

/**
 * A tree flydown input control that displays flydown options in optgroups
 *
 * The tree for a grouped flydown may be at most 3 levels deep including the
 * root node.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class GroupedFlydown extends TreeFlydown
{
    // {{{ public function setTree()

    /**
     * Sets the tree to use for display
     *
     * The tree for a grouped flydown may be at most 3 levels deep including
     * the root node.
     *
     * @param Model\TreeFlydownNode $tree the tree to use for display.
     *
     * @throws Exception\Exception if the tree more than 3 levels deep.
     */
    public function setTree(Model\TreeFlydownNode $tree)
    {
        $this->checkTree($tree);
        parent::setTree($tree);
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this grouped flydown
     *
     * Displays this flydown as a XHTML select. Level 1 tree nodes are
     * displayed as optgroups if their value is null, they have children and
     * they are not dividers.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        Widget::display();

        // tree is copied for display so we can add a blank node if show_blank
        // is true
        $display_tree = $this->getDisplayTree();
        $count = count($display_tree) - 1;

        // only show a select if there is more than one option
        if ($count > 1) {

            $select_tag = new Html\Tag('select');
            $select_tag->name = $this->id;
            $select_tag->id = $this->id;
            $select_tag->class = $this->getCSSClassString();

            if (!$this->isSensitive())
                $select_tag->disabled = 'disabled';

            $select_tag->open();

            foreach ($display_tree->getChildren() as $child)
                $this->displayNode($child, 1);

            $select_tag->close();

        } elseif ($count == 1) {
            // get first and only element
            $option = reset($display_tree->getChildren())->getOption();
            $this->displaySingle($option);
        }
    }

    // }}}
    // {{{ protected function checkTree()

    /**
     * Checks a tree to ensure it is valid for a grouped flydown
     *
     * @param Model\TreeFlydownNode the tree to check.
     *
     * @throws Exception\Exception if the tree is not valid for a grouped
     *         flydown.
     */
    protected function checkTree(Model\TreeFlydownNode $tree, $level = 0)
    {
        if ($level > 2) {
            throw new Exception\Exception(
                'GroupedFlydown tree must not be more than 3 levels '.
                'including the root node.'
            );
        }

        foreach ($tree->getChildren() as $child)
            $this->checkTree($child, $level + 1);
    }

    // }}}
    // {{{ protected function displayNode()

    /**
     * Displays a grouped tree flydown node and its child nodes
     *
     * Level 1 tree nodes are displayed as optgroups if their value is null,
     * they have children and they are not dividers.
     *
     * @param Model\TreeFlydownNode $node     the node to display.
     * @param integer               $level    the current level of the tree
     *                                        node.
     * @param array                 $path     an array of values representing
     *                                        the tree path to this node.
     * @param boolean               $selected whether or not an element has
     *                                        been selected yet.
     */
    protected function displayNode(Model\TreeFlydownNode $node, $level = 0,
        $selected = false)
    {
        $children = $node->getChildren();
        $flydown_option = $node->getOption();

        if ($level == 1 && count($children) > 0 &&
            end($flydown_option->value) === null &&
            !$flydown_option instanceof Model\FlydownDivider) {

            $optgroup_tag = new Html\Tag('optgroup');
            $optgroup_tag->label = $flydown_option->title;
            $optgroup_tag->open();
            foreach($node->getChildren() as $child_node)
                $this->displayNode($child_node, $level + 1, $selected);

            $optgroup_tag->close();
        } else {
            $option_tag = new Html\Tag('option');

            if ($this->serialize_values) {
                $salt = $this->getForm()->getSalt();
                $option_tag->value = Util\String::signedSerialize(
                    $flydown_option->value,
                    $salt
                );
            } else {
                $option_tag->value = (string)$flydown_option->values;
            }

            if ($flydown_option instanceof Model\FlydownDivider) {
                $option_tag->disabled = 'disabled';
                $option_tag->class = 'swat-flydown-option-divider';
            } else {
                $option_tag->removeAttribute('disabled');
                $option_tag->removeAttribute('class');
            }

            if ($this->path === $flydown_option->value &&
                $selected === false &&
                !$flydown_option instanceof Model\FlydownDivider) {

                $option_tag->selected = 'selected';
                $selected = true;
            } else {
                $option_tag->removeAttribute('selected');
            }

            $option_tag->setContent($flydown_option->title);
            $option_tag->display();

            foreach($children as $child_node)
                $this->displayNode($child_node, $level + 1, $selected);
        }
    }

    // }}}
    // {{{ protected function buildDisplayTree()

    /**
     * Builds this grouped flydown's display tree by copying nodes from this
     * grouped flydown's tree
     *
     * @param Model\TreeFlydownNode $tree   the source tree node to build from.
     * @param Model\TreeFlydownNode $parent the destination parent node to add
     *                                      display tree nodes to.
     * @param array                 $path   the current path of the display
     *                                      tree.
     */
    protected function buildDisplayTree(Model\TreeFlydownNode $tree,
        Model\TreeFlydownNode $parent, $path = array())
    {
        $flydown_option = $tree->getOption();
        $path[] = $flydown_option->value;
        $new_node = new Model\TreeFlydownNode($path, $flydown_option->title);

        $parent->addChild($new_node);
        foreach ($tree->getChildren() as $child)
            $this->buildDisplayTree($child, $new_node, $path);
    }

    // }}}
    // {{{ protected function getDisplayTree()

    /**
     * Gets the display tree of this grouped flydown
     *
     * The display tree is copied from this grouped flydown's tree. If
     * {@link GroupedFlydown::$show_blank} is true, a blank node is inserted
     * as the first child of the root node of the display tree.
     *
     * The value of a display tree node is set to an array representing the
     * path to the node in the display tree.
     *
     * @see setTree()
     */
    protected function getDisplayTree()
    {
        $display_tree = new Model\TreeFlydownNode(null, 'root');
        if ($this->show_blank) {
            $display_tree->addChild(
                new Model\TreeFlydownNode(
                    null,
                    $this->blank_title
                )
            );
        }

        foreach ($this->tree->getChildren() as $child)
            $this->buildDisplayTree($child, $display_tree);

        return $display_tree;
    }

    // }}}
}

?>
