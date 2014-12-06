<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\Model;
use Silverorange\Swat\Util;

/**
 * A checkbox array widget formatted into a tree
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class CheckboxTree extends CheckboxList implements Model\State
{
    // {{{ protected properties

    /**
     * Checkbox tree structure
     *
     * An tree structure of {@link Model\TreeNode} objects. This structure
     * overwrites the public options property.
     *
     * @var Model\DataTreeNode
     */
    protected $tree = null;

    /**
     * A label tag used for displaying tree nodes
     *
     * @var Html\Tag
     *
     * @see CheckboxTree::displayNode()
     */
    protected $label_tag = null;

    /**
     * An input tag used for displaying tree nodes
     *
     * @var Html\Tag
     *
     * @see CheckboxTree::displayNode()
     */
    protected $input_tag = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new checkbox list
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see CheckboxList::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->setTree(new Model\DataTreeNode(null, 'root'));
    }

    // }}}
    // {{{ public function display()

    public function display()
    {
        if (!$this->visible)
            return;

        Widget::display();

        $this->getForm()->addHiddenField($this->id.'_submitted', 1);

        $div_tag = new Html\Tag('div');
        $div_tag->id = $this->id;
        $div_tag->class = $this->getCSSClassString();
        $div_tag->open();

        $this->label_tag = new Html\Tag('label');
        $this->label_tag->class = 'swat-control';

        $this->input_tag = new Html\Tag('input');
        $this->input_tag->type = 'checkbox';
        $this->input_tag->name = $this->id.'[]';

        if ($this->tree !== null)
            $num_nodes = $this->displayNode($this->tree);
        else
            $num_nodes = 0;

        // Only display the check-all widget if more than one checkable item is
        // displayed.
        $check_all = $this->getCompositeWidget('check_all');
        $check_all->visible = ($num_nodes > 1 && $this->show_check_all);
        $check_all->display();

        $div_tag->close();

        Util\JavaScript::displayInline($this->getInlineJavaScript());
    }

    // }}}
    // {{{ public function setTree()

    /**
     * Sets the tree to use for display
     *
     * @param Model\DataTreeNode $tree the tree to use for display.
     */
    public function setTree(Model\DataTreeNode $tree)
    {
        $this->tree = $tree;
    }

    // }}}
    // {{{ public function getTree()

    /**
     * Gets the tree collection of {@link Model\TreeNode} objects for this
     * tree flydown
     *
     * @return Model\TreeNode Tree of nodes
     */
    public function getTree()
    {
        return $this->tree;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this checkbox tree
     *
     * @return array the array of CSS classes that are applied to this checkbox
     *               tree.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-checkbox-tree');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
    // {{{ private function displayNode()

    /**
     * Displays a node in a tree as a checkbox input
     *
     * @param Model\DataTreeNode $node         the node to display.
     * @param integer            $nodes        the current number of nodes.
     * @param string             $parent_index the path of the parent node.
     *
     * @return integer the number of checkable nodes in the tree.
     */
    private function displayNode(Model\DataTreeNode $node, $nodes = 0,
        $parent_index = '')
    {
        // build a unique id of the indexes of the tree
        if ($parent_index === '' || $parent_index === null) {
            // index of the first node is just the node index
            $index = $node->getIndex();
        } else {
            // index of other nodes is a combination of parent indexes
            $index = $parent_index.'.'.$node->getIndex();

            echo '<li>';

            if (isset($node->value)) {
                $this->input_tag->id = $this->id.'_'.$index;
                $this->input_tag->value = $node->value;

                if (in_array($node->value, $this->values))
                    $this->input_tag->checked = 'checked';
                else
                    $this->input_tag->checked = null;

                if (!$this->isSensitive())
                    $this->input_tag->disabled = 'disabled';

                $this->label_tag->for = $this->id.'_'.$index;
                $this->label_tag->setContent($node->title);

                echo '<span class="swat-checkbox-wrapper">';
                $this->input_tag->display();
                echo '<span class="swat-checkbox-shim"></span>';
                echo '</span>';
                $this->label_tag->display();
            } else {
                echo Util\String::minimizeEntities($node->title);
            }
        }

        // display children
        $child_nodes = $node->getChildren();
        if (count($child_nodes) > 0) {
            echo '<ul>';
            foreach ($child_nodes as $child_node) {
                $nodes = $this->displayNode($child_node, $nodes, $index);
            }
            echo '</ul>';
        }

        if ($parent_index !== '' && $parent_index !== null) {
            echo '</li>';
        }

        // count checkable nodes
        if ($node->value !== null)
            $nodes++;

        return $nodes;
    }

    // }}}
}
