<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/SwatTreeNode.php';
require_once 'Swat/SwatOption.php';

/**
 * A tree node for a flydown
 *
 * Contains a flydown option that has a value and a title.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license
 */
class SwatTreeFlydownNode extends SwatTreeNode
{
    // {{{ protected properties

    /**
     * The flydown option for this node
     *
     * @var SwatOption
     */
    protected $flydown_option;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new tree flydown node
     *
     * This method is overloaded to accept either a value-title pair or a new
     * {@link SwatOption} object. Example usage:
     *
     * <code>
     * // using an already existing flydown option
     * $option = new SwatOption(1, 'Apples');
     * $node1 = new SwatTreeFlydownNode($option);
     *
     * // creating a new flydown option
     * $node2 = new SwatTreeFlydown(2, 'Oranges');
     * </code>
     *
     * @param mixed $param1 either a {@link SwatOption} object or an
     *                      integer or string representing the value of a new
     *                      flydown option.
     * @param mixed $param2 if a SwatOption object is passed in for
     *                      parameter one, this parameter must be ommitted.
     *                      Otherwise, this is a string title for a new
     *                      flydown option.
     *
     * @throws SwatException
     */
    public function __construct($param1, $param2 = null)
    {
        if ($param2 === null && $param1 instanceof SwatOption)
            $this->flydown_option = $param1;
        elseif ($param2 === null)
            throw new SwatException('First parameter must be a '.
                'SwatOption or second parameter must be specified.');
        else
            $this->flydown_option = new SwatOption($param1, $param2);
    }

    // }}}
    // {{{ public function getOption()

    /**
     * Gets the option for this node
     *
     * @return SwatOption the option for this node.
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
     * @param SwatTreeNode $child the child node to add to this node.
     */
    public function addChild($child)
    {
        if ($child instanceof SwatDataTreeNode)
            $child = SwatTreeFlydownNode::convertFromDataTree($child);

        parent::addChild($child);
    }

    // }}}
    // {{{ public staticfunction convertFromDataTree()

    public static function convertFromDataTree(SwatDataTreeNode $tree)
    {
        $new_tree = new SwatTreeFlydownNode($tree->value, $tree->title);

        foreach ($tree->getChildren() as $child_node)
            $new_tree->addChild(self::convertFromDataTree($child_node));

        return $new_tree;
    }

    // }}}
}

?>
