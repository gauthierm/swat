<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Model;

use Silverorange\Swat\Exception;

/**
 * A tree node for a flydown
 *
 * Contains a flydown option that has a value and a title.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license
 */
class TreeFlydownNode extends TreeNode
{
    // {{{ protected properties

    /**
     * The flydown option for this node
     *
     * @var Option
     */
    protected $flydown_option;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new tree flydown node
     *
     * This method is overloaded to accept either a value-title pair or a new
     * {@link Option} object. Example usage:
     *
     * <code>
     * // using an already existing flydown option
     * $option = new Option(1, 'Apples');
     * $node1 = new TreeFlydownNode($option);
     *
     * // creating a new flydown option
     * $node2 = new TreeFlydown(2, 'Oranges');
     * </code>
     *
     * @param mixed $param1 either an {@link Option} object; or an integer or a
     *                      string representing the value of a new flydown
     *                      option.
     * @param mixed $param2 if an Option object is passed in for the first
     *                      parameter, this parameter must be ommitted.
     *                      Otherwise, this is a string title for a new
     *                      flydown option.
     *
     * @throws Exception\Exception
     */
    public function __construct($param1, $param2 = null)
    {
        if ($param2 === null && $param1 instanceof Option) {
            $this->flydown_option = $param1;
        } elseif ($param2 === null) {
            throw new Exception\Exception(
                'First parameter must be a Silverorange\Swat\Model\Option or '.
                'second parameter must be specified.'
            );
        } else {
            $this->flydown_option = new Option($param1, $param2);
        }
    }

    // }}}
    // {{{ public function getOption()

    /**
     * Gets the option for this node
     *
     * @return Option the option for this node.
     */
    public function getOption()
    {
        return $this->flydown_option;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child node to this node
     *
     * The parent of the child node is set to this node.
     *
     * @param TreeNode $child the child node to add to this node.
     */
    public function addChild(TreeNode $child)
    {
        if ($child instanceof DataTreeNode) {
            $child = TreeFlydownNode::convertFromDataTree($child);
        }

        parent::addChild($child);
    }

    // }}}
    // {{{ public staticfunction convertFromDataTree()

    public static function convertFromDataTree(DataTreeNode $tree)
    {
        $new_tree = new TreeFlydownNode($tree->value, $tree->title);

        foreach ($tree->getChildren() as $child_node)
            $new_tree->addChild(self::convertFromDataTree($child_node));

        return $new_tree;
    }

    // }}}
}

?>
