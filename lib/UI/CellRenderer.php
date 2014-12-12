<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Model;
use Silverorange\Swat\Exception;

/**
 * Object for rendering a single cell
 *
 * Subclasses add public class variable to store data they need for rendering.
 *
 * @package   Swat
 * @copyright 2004-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class CellRenderer extends Object
{
    // {{{ public properties

    /**
     * A non-visible unique id for this cell renderer, or null
     *
     * @var string
     */
    public $id = null;

    /**
     * Sensitive
     *
     * Whether this renderer is sensitive. If a renderer is sensitive it reacts
     * to user input. Unsensitive renderers should display "grayed-out" to
     * inform the user they are not sensitive. All renderers that react to
     * user input should respect this property in their display() method.
     *
     * @var boolean
     */
    public $sensitive = true;

    // }}}
    // {{{ private properties

    /**
     * An array containing the static properties of this cell renderer
     *
     * @var array
     */
    private $static_properties = array();

    /**
     * How many times this cell renderer was rendered
     *
     * @var integer
     */
    private $render_count = 0;

    /**
     * Composite renderers of this renderer
     *
     * Array is of the form 'key' => renderer.
     *
     * @var array
     */
    private $composite_renderers = array();

    /**
     * Whether or not composite renderers have been created
     *
     * This flag is used by the
     * {@link CellRenderer::confirmCompositeRenderers()} method to ensure
     * composite renderers are only created once.
     *
     * @var boolean
     */
    private $composite_renderers_created = false;

    // }}}
    // {{{ public function render()

    /**
     * Renders this cell
     *
     * Renders this cell using the values currently stored in class variables.
     *
     * Cell renderer subclasses should extend this method to do all output
     * neccessary to display the cell.
     */
    public function render()
    {
        $this->render_count++;
    }

    // }}}
    // {{{ public function init()

    /**
     * Called during the init phase
     *
     * Sub-classes can redefine this method to perform any necessary processing.
     */
    public function init()
    {
        foreach ($this->getCompositeRenderers() as $renderer) {
            $renderer->init();
        }
    }

    // }}}
    // {{{ public function process()

    /**
     * Called during processing phase
     *
     * Sub-classes can redefine this method to perform any necessary processing.
     */
    public function process()
    {
        foreach ($this->getCompositeRenderers() as $renderer) {
            $renderer->process();
        }
    }

    // }}}
    // {{{ public function getMessages()

    /**
     * Gathers all messages from this cell renderer
     *
     * By default, cell renderers do not have messages. Subclasses may override
     * this method to return messages.
     *
     * @return array an array of {@link Modle\Message} objects.
     */
    public function getMessages()
    {
        return array();
    }

    // }}}
    // {{{ public function hasMessage()

    /**
     * Gets whether or not this cell renderer has messages
     *
     * By default, cell renderers do not have messages. Subclasses may override
     * this method if they have messages.
     *
     * @return boolean true if this cell renderer has one or more messages and
     *                 false if it does not.
     */
    public function hasMessage()
    {
        return false;
    }

    // }}}
    // {{{ public function getPropertyNameToMap()

    /**
     * Get a property name to use for mapping
     *
     * This method can be overridden by sub-classes that need to modify the
     * name of a property mapping.  This allows cell renderers which contain
     * multiple UI objects to mangle property names if necessary to avoid
     * conflicts.
     *
     * @param Object $object the object containing the property that is being
     *                       mapped. Usually this is the cell renderer itself,
     *                       but not necessarily. It could be a UI object
     *                       within this cell renderer.
     * @param string $name   the name of the property being mapped.
     *
     * @return string the name of the property to actually map. This property
     *                should either exist as a public property of the cell
     *                renderer or be handled by a magic __set() method.
     */
    public function getPropertyNameToMap(Object $object, $name)
    {
        return $name;
    }

    // }}}
    // {{{ public function getInlineJavaScript()

    /**
     * Gets ths inline JavaScript required by this cell renderer
     *
     * @return string the inline JavaScript required by this cell renderer.
     */
    public function getInlineJavaScript()
    {
        return '';
    }

    // }}}
    // {{{ public function getBaseCSSClassNames()

    /**
     * Gets the base CSS class names for this cell renderer
     *
     * This is the recommended place for cell-renderer subclasses to add extra
     * hard-coded CSS classes.
     *
     * @return array the array of base CSS class names for this cell renderer.
     */
    public function getBaseCSSClassNames()
    {
        return array();
    }

    // }}}
    // {{{ public function getDataSpecificCSSClassNames()

    /**
     * Gets the data specific CSS class names for this cell renderer
     *
     * This is the recommended place for cell-renderer subclasses to add extra
     * hard-coded CSS classes that depend on data-bound properties of this
     * cell-renderer.
     *
     * @return array the array of base CSS class names for this cell renderer.
     */
    public function getDataSpecificCSSClassNames()
    {
        return array();
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this cell renderer
     *
     * If this renderer has never been rendered, an empty set is returned to
     * reduce the number of required HTTP requests.
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this cell
     *                          cell renderer.
     */
    public function getHtmlHeadEntrySet()
    {
        if ($this->render_count > 0) {
            $set = new Html\ResourceSet($this->html_head_entry_set);
        } else {
            $set = new Html\ResourceSet();
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that may be needed by this cell
     * renderer
     *
     * @return Html\ResourceSet the Html\Resource objects that may be needed by
     *                          this cell renderer.
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        return new Html\ResourceSet($this->html_head_entry_set);
    }

    // }}}
    // {{{ public function isPropertyStatic()

    /**
     * Checks if a public property is static (can not be data-mapped)
     *
     * This method takes a property name and returns a boolean representing
     * weather or not the property has been made static.
     *
     * @param string $property_name the property name to check.
     *
     * @return boolean true if the property is static and false if the property
     *                 may be data-mapped.
     *
     * @see ::makePropertyStatic()
     */
    public function isPropertyStatic($property_name)
    {
        return (in_array($property_name, $this->static_properties));
    }

    // }}}
    // {{{ final public function getInheritanceCSSClassNames()

    /**
     * Gets the CSS class names of this cell renderer based on the inheritance
     * tree for this cell renderer
     *
     * For example, a class with the following ancestry:
     *
     * CellRenderer -> TextCellRenderer -> NullTextCellRenderer
     *
     * will return the following array of class names:
     *
     * <code>
     * array(
     *    'swat-cell-renderer',
     *    'swat-text-cell-renderer',
     *    'swat-null-text-cell-renderer',
     * );
     * </code>
     *
     * @return array the array of CSS class names based on an inheritance tree
     *               for this cell renderer.
     */
    final public function getInheritanceCSSClassNames()
    {
        $prefix = 'swat-';
        $php_class_name = get_class($this);
        $css_class_names = array();

        $ns_length = strlen(__NAMESPACE__);

        // get the ancestors that are swat classes
        while (strcmp($php_class_name, __CLASS__) !== 0) {
            if (strncmp($php_class_name, __NAMESPACE__, $ns_length) === 0) {
                $unqualified_class_name = end(explode('\\', $php_class_name));

                // Convert CamelCase to dashed-names
                $css_class_name = strtolower(
                    preg_replace('/([A-Z])/u', '-\1', $unqualified_class_name)
                );

                // If class name had two or more parts, it has an extra dash
                // at the beginning
                if ($css_class_name[0] === '-') {
                    $css_class_name = substr($css_class_name, 1);
                }

                // Add the swat- prefix
                $css_class_name = $prefix . $css_class_name;

                array_unshift($css_class_names, $css_class_name);
            }
            $php_class_name = get_parent_class($php_class_name);
        }

        return $css_class_names;
    }

    // }}}
    // {{{ protected function createCompositeRenderers()

    /**
     * Creates and adds composite renderers of this renderer
     *
     * Created composite renderers should be added in this method using
     * {@link CellRenderer::addCompositeRenderer()}.
     */
    protected function createCompositeRenderers()
    {
    }

    // }}}
    // {{{ final protected function addCompositeRenderer()

    /**
     * Adds a composite a renderer to this renderer
     *
     * @param CellRenderer $renderer the composite renderer to add.
     * @param string       $key      a key identifying the renderer so it may
     *                               be retrieved later. The key has to be
     *                               unique within this renderer relative to
     *                               the keys of other composite renderers.
     *
     * @throws Exception\DuplicateIdException if a composite renderer with the
     *         specified key is already added to this renderer.
     *
     * @throws Exception\Exception if the specified renderer is already the
     *         child of another object.
     */
    final protected function addCompositeRenderer(
        CellRenderer $renderer,
        $key
    ) {
        if (array_key_exists($key, $this->composite_renderers)) {
            throw new Exception\DuplicateIdException(
                sprintf(
                    "A composite renderer with the key '%s' already exists " .
                    "in this renderer.",
                    $key
                ),
                0,
                $key
            );
        }

        if ($renderer->parent !== null) {
            throw new Exception\Exception(
                'Cannot add a composite renderer that already has a parent.'
            );
        }

        $this->composite_renderers[$key] = $renderer;
        $renderer->parent = $this;
    }

    // }}}
    // {{{ final protected function getCompositeRenderer()

    /**
     * Gets a composite renderer of this renderer by the composite renderer's
     * key
     *
     * This is used by other methods to retrieve a specific composite renderer.
     * This method ensures composite renderers are created before trying to
     * retrieve the specified renderer.
     *
     * @param string $key the key of the composite renderer to get.
     *
     * @return CellRenderer the specified composite renderer.
     *
     * @throws Exception\WidgetNotFoundException if no composite renderer with
     *         the specified key exists in this renderer.
     */
    final protected function getCompositeRenderer($key)
    {
        $this->confirmCompositeRenderers();

        if (!array_key_exists($key, $this->composite_renderers)) {
            throw new Exception\WidgetNotFoundException(
                sprintf(
                    "Composite renderer with key of '%s' not found in %s. " .
                    "Make sure the composite renderer was created and " .
                    "added to this renderer.",
                    $key,
                    get_class($this)
                ),
                0,
                $key
            );
        }

        return $this->composite_renderers[$key];
    }

    // }}}
    // {{{ final protected function getCompositeRenderers()

    /**
     * Gets all composite renderers added to this renderer
     *
     * This method ensures composite renderers are created before retrieving
     * the renderers.
     *
     * @param string $class_name optional class name. If set, only renderers
     *                           that are instances of <code>$class_name</code>
     *                           are returned.
     *
     * @return array all composite wigets added to this renderer. The array is
     *               indexed by the composite renderer keys.
     *
     * @see CellRenderer::addCompositeRenderer()
     */
    final protected function getCompositeRenderers($class_name = null)
    {
        $this->confirmCompositeRenderers();

        if (!($class_name === null ||
            class_exists($class_name) || interface_exists($class_name))) {
            return array();
        }

        $out = array();

        foreach ($this->composite_renderers as $key => $renderer) {
            if ($class_name === null || $renderer instanceof $class_name) {
                $out[$key] = $renderer;
            }
        }

        return $out;
    }

    // }}}
    // {{{ final protected function confirmCompositeRenderers()

    /**
     * Confirms composite renderers have been created
     *
     * Renderers are only created once. This method may be called multiple
     * times in different places to ensure composite renderers are available.
     * In general, it is best to call this method before attempting to use
     * composite renderers.
     *
     * This method is called by the default implementations of init(),
     * process() and is called any time
     * {@link CellRenderer::getCompositeRenderer()} is called so it rarely
     * needs to be called manually.
     */
    final protected function confirmCompositeRenderers()
    {
        if (!$this->composite_renderers_created) {
            $this->createCompositeRenderers();
            $this->composite_renderers_created = true;
        }
    }

    // }}}
    // {{{ final protected function makePropertyStatic()

    /**
     * Make a public property static
     *
     * This method takes a property name and marks it as static, meaning that
     * a user can not data-map this property.
     *
     * @param $property_name string the property name.
     *
     * @see CellRenderer::isPropertyStatic()
     *
     * @throws Exception\InvalidPropertyException if the specified
     *         <i>$property_name</i> is not a non-public static property of
     *         this class.
     */
    final protected function makePropertyStatic($property_name)
    {
        $reflector = new \ReflectionObject($this);
        if ($reflector->hasProperty($property_name)) {
            $property = $reflector->getProperty($property_name);
            if ($property->isPublic() && !$property->isStatic()) {
                $this->static_properties[] = $property_name;
            } else {
                throw new Exception\InvalidPropertyException(
                    "Property {$property_name} is not a non-public static " .
                    "property and cannot be made static.",
                    0,
                    $this,
                    $property_name
                );
            }
        } else {
            throw new Exception\InvalidPropertyException(
                "Can not make non-existant property {$property_name} static.",
                0,
                $this,
                $property_name
            );
        }
    }

    // }}}
}
