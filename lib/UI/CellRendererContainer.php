<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Exception;

/**
 * Abstract base class for objects which contain cell renderers.
 *
 * @package   Swat
 * @copyright 2006-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class CellRendererContainer extends UIObject implements UIParent
{
    // {{{ protected properties

    /**
     * The set of CellRenderer objects contained in this container
     *
     * @var CellRendererSet
     */
    protected $renderers = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new cell renderer container
     */
    public function __construct()
    {
        parent::__construct();
        $this->renderers = new CellRendererSet();
    }

    // }}}
    // {{{ public function addMappingToRenderer()

    /**
     * Links a data-field to a cell renderer property of a cell renderer
     * within this container
     *
     * @param CellRenderer $renderer   the cell renderer in this container onto
     *                                 which the data-field is to be mapped.
     * @param string       $data_field the field of the data model to map to
     *                                 the cell renderer property.
     * @param string       $property   the property of the cell renderer to
     *                                 which the <i>$data_field</i> is mapped.
     * @param UIObject     $object     optional. The object containing the
     *                                 property to map when the property does
     *                                 not belong to the cell renderer itself.
     *                                 If unspecified, the <i>$property</i>
     *                                 must be a property of the given cell
     *                                 renderer.
     *
     * @return CellRendererMapping a new mapping object that has been added to
     *                             the renderer.
     */
    public function addMappingToRenderer($renderer, $data_field, $property,
        $object = null)
    {
        if ($object !== null)
            $property = $renderer->getPropertyNameToMap($object, $property);

        $mapping = new CellRendererMapping($property, $data_field);
        $this->renderers->addMappingToRenderer($renderer, $mapping);

        if ($object !== null)
            $object->$property = $mapping;

        return $mapping;
    }

    // }}}
    // {{{ public function addRenderer()

    /**
     * Adds a cell renderer to this container's set of renderers
     *
     * @param CellRenderer $renderer the renderer to add.
     */
    public function addRenderer(CellRenderer $renderer)
    {
        $this->renderers->addRenderer($renderer);
        $renderer->parent = $this;
    }

    // }}}
    // {{{ public function getRenderers()

    /**
     * Gets the cell renderers of this container
     *
     * @return array an array containing the cell renderers in this container.
     */
    public function getRenderers()
    {
        $out = array();
        $renderers = clone $this->renderers;
        foreach ($renderers as $renderer)
            $out[] = $renderer;

        return $out;
    }

    // }}}
    // {{{ public function getRenderer()

    /**
     * Gets a cell renderer of this container by its unique identifier
     *
     * @param string $renderer_id the unique identifier of the cell renderer
     *                            to get.
     *
     * @return CellRenderer the cell renderer of this container with the
     *                      provided unique identifier.
     *
     * @throws Exception\ObjectNotFoundException if a renderer with the given
     *         <i>$renderer_id</i> does not exist in this container.
     */
    public function getRenderer($renderer_id)
    {
        return $this->renderers->getRenderer($renderer_id);
    }

    // }}}
    // {{{ public function getRendererByPosition()

    /**
     * Gets a cell renderer in this container based on its ordinal position
     *
     * @param integer $position the ordinal position of the cell renderer to
     *                          get. The position is zero-based.
     *
     * @return CellRenderer the renderer at the specified ordinal position.
     *
     * @throws Exception\ObjectNotFoundException if the requested
     *         <i>$position</i> is greater than the number of cell renderers in
     *         this container.
     */
    public function getRendererByPosition($position = 0)
    {
        return $this->renderers->getRendererByPosition($position);
    }

    // }}}
    // {{{ public function getFirstRenderer()

    /**
     * Gets the first cell renderer in this container
     *
     * @return CellRenderer the first cell renderer in this container or null
     *                      if this container contains no cell renderers.
     */
    public function getFirstRenderer()
    {
        return $this->renderers->getFirst();
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Add a child object to this object
     *
     * @param CellRenderer $child the reference to the child object to add.
     *
     * @throws Exception\InvalidClassException if the given <i>$child</i> is
     *         not an instance of {@link CellRenderer}.
     *
     * @see UIParent::addChild()
     */
    public function addChild(UIObject $child)
    {
        if ($child instanceof CellRenderer) {
            $this->addRenderer($child);
        } else {
            throw new Exception\InvalidClassException(
                'Only \Silverorange\Swat\UI\CellRender objects may be nested '.
                'within '.get_class($this).' objects.',
                0,
                $child
            );
        }
    }

    // }}}
    // {{{ public function getDescendants()

    /**
     * Gets descendant UI-objects
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this cell renderer container.
     *               If descendant objects have identifiers, the identifier is
     *               used as the array key.
     *
     * @see UIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (!($class_name === null ||
            class_exists($class_name) || interface_exists($class_name)))
            return array();

        $out = array();

        foreach ($this->getRenderers() as $renderer) {
            if ($class_name === null || $renderer instanceof $class_name) {
                if ($renderer->id === null)
                    $out[] = $renderer;
                else
                    $out[$renderer->id] = $renderer;
            }

            if ($renderer instanceof UIParent) {
                $out = array_merge(
                    $out,
                    $renderer->getDescendants($class_name)
                );
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getFirstDescendant()

    /**
     * Gets the first descendant UI-object of a specific class
     *
     * @param string $class_name class name to look for.
     *
     * @return UIObject the first descendant UI-object or null if no matching
     *                  descendant is found.
     *
     * @see UIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name))
            return null;

        $out = null;

        foreach ($this->getRenderers() as $renderer) {
            if ($renderer instanceof $class_name) {
                $out = $renderer;
                break;
            }

            if ($renderer instanceof UIParent) {
                $out = $renderer->getFirstDescendant($class_name);
                if ($out !== null)
                    break;
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this cell renderer container.
     *
     * @return array an array of UI-object states with UI-object identifiers as
     *               array keys.
     */
    public function getDescendantStates()
    {
        $states = array();

        $state = '\Silverorange\Swat\Model\State';
        foreach ($this->getDescendants($state) as $id => $object) {
            $states[$id] = $object->getState();
        }

        return $states;
    }

    // }}}
    // {{{ public function setDescendantStates()

    /**
     * Sets descendant states
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * cell renderer container.
     *
     * @param array $states an array of UI-object states with UI-object
     *                      identifiers as array keys.
     */
    public function setDescendantStates(array $states)
    {
        $state = '\Silverorange\Swat\Model\State';
        foreach ($this->getDescendants($state) as $id => $object) {
            if (isset($states[$id])) {
                $object->setState($states[$id]);
            }
        }
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this cell renderer
     * container
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this cell
     *                          renderer container.
     *
     * @see UIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        $renderers = $this->getRenderers();
        foreach ($renderers as $renderer) {
            $set->addEntrySet($renderer->getHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this cell renderer
     * container
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed by
     *                          this cell renderer container.
     *
     * @see UIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();
        $renderers = $this->getRenderers();
        foreach ($renderers as $renderer) {
            $set->addEntrySet($renderer->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getRendererInlineJavaScript()

    /**
     * Gets inline JavaScript used by all cell renderers within this cell
     * renderer container
     *
     * @return string the inline JavaScript used by all cell renderers within
     *                this cell renderer container.
     */
    public function getRendererInlineJavaScript()
    {
        $javascript = '';

        foreach ($this->getRenderers() as $renderer) {
            $renderer_javascript = $renderer->getInlineJavaScript();
            if ($renderer_javascript != '')
                $javascript = "\n".$renderer_javascript;
        }

        return $javascript;
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                          objects in the UI tree.
     *
     * @return UIObject a deep copy of the UI tree starting with this UI
     *                  object.
     *
     * @see UIObject::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);
        $copy->renderers = new CellRendererSet();

        foreach ($this->renderers as $renderer) {
            $copy_renderer = $renderer->copy($id_suffix);
            $copy_renderer->parent = $copy;
            $copy->renderers->addRenderer($copy_renderer);

            $copy_mappings = array();
            $mappings = $this->renderers->getMappingsByRenderer($renderer);
            foreach ($mappings as $mapping)
                $copy_mappings[] = clone $mapping;

            $copy->renderers->addMappingsToRenderer(
                $copy_renderer, $copy_mappings);
        }

        return $copy;
    }

    // }}}
}

?>
