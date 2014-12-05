<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;

/**
 * A base class for Swat user-interface elements
 *
 * TODO: describe our conventions on how CSS classes and XHTML ids are
 * displayed.
 *
 * @package   Swat
 * @copyright 2006-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class Object
{
    // {{{ public properties

    /**
     * The object which contains this object
     *
     * @var Object
     */
    public $parent = null;

    /**
     * Visible
     *
     * Whether this UI object is displayed. All UI objects should respect this.
     *
     * @var boolean
     *
     * @see Object::isVisible()
     */
    public $visible = true;

    /**
     * A user-specified array of CSS classes that are applied to this
     * user-interface object
     *
     * See the class-level documentation for Object for details on how CSS
     * classes and XHTML ids are displayed on user-interface objects.
     *
     * @var array
     */
    public $classes = array();

    // }}}
    // {{{ protected properties

    /**
     * A set of HTML head entries needed by this user-interface element
     *
     * Entries are stored in a data object called {@link Html\Resource}.
     * This property contains a set of such objects.
     *
     * @var Html\ResourceSet
     */
    protected $html_head_entry_set;

    // }}}
    // {{{ public function __construct()

    public function __construct()
    {
        $this->html_head_entry_set = new Html\ResourceSet();
    }

    // }}}
    // {{{ public function addStyleSheet()

    /**
     * Adds a stylesheet to the list of stylesheets needed by this
     * user-interface element
     *
     * @param string  $stylesheet    the uri of the style sheet.
     * @param integer $display_order the relative order in which to display
     *                               this stylesheet head entry.
     *
     * @throws Exception\Exception
     */
    public function addStyleSheet($stylesheet)
    {
        if ($this->html_head_entry_set === null) {
            throw new Exception\Exception(
                sprintf(
                    "Child class '%s' did not instantiate a HTML resource " .
                    "set. This should be done in the constructor either by " .
                    "calling parent::__construct() or by creating a new " .
                    "HTML resource set.",
                    get_class($this)
                )
            );
        }

        $this->html_head_entry_set->addEntry(
            new Html\StyleSheetResource($stylesheet)
        );
    }

    // }}}
    // {{{ public function addJavaScript()

    /**
     * Adds a JavaScript include to the list of JavaScript includes needed
     * by this user-interface element
     *
     * @param string  $java_script   the uri of the JavaScript include.
     * @param integer $display_order the relative order in which to display
     *                               this JavaScript head entry.
     */
    public function addJavaScript($java_script)
    {
        if ($this->html_head_entry_set === null) {
            throw new Exception\Exception(
                sprintf(
                    "Child class '%s' did not instantiate a HTML resource " .
                    "set. This should be done in the constructor either by " .
                    "calling parent::__construct() or by creating a new " .
                    "HTML resource set.",
                    get_class($this)
                )
            );
        }

        $this->html_head_entry_set->addEntry(
            new Html\JavaScriptResource($java_script)
        );
    }

    // }}}
    // {{{ public function addComment()

    /**
     * Adds a comment to the list of HTML head entries needed by this user-
     * interface element
     *
     * @param string $comment the contents of the comment to include.
     */
    public function addComment($comment)
    {
        if ($this->html_head_entry_set === null) {
            throw new Exception\Exception(
                sprintf(
                    "Child class '%s' did not instantiate a HTML resource " .
                    "set. This should be done in the constructor either by " .
                    "calling parent::__construct() or by creating a new " .
                    "HTML resource set.",
                    get_class($this)
                )
            );
        }

        $this->html_head_entry_set->addEntry(
            new Html\CommentResource($comment)
        );
    }

    // }}}
    // {{{ public function addInlineScript()

    public function addInlineScript($script)
    {
        $this->inline_scripts->add($script);
    }

    // }}}
    // {{{ public function getFirstAncestor()

    /**
     * Gets the first ancestor object of a specific class
     *
     * Retrieves the first ancestor object in the parent path that is a
     * descendant of the specified class name.
     *
     * @param string $class_name class name to look for.
     *
     * @return mixed the first ancestor object or null if no matching ancestor
     *               is found.
     *
     * @see Parent::getFirstDescendant()
     */
    public function getFirstAncestor($class_name)
    {
        if (!class_exists($class_name)) {
            return null;
        }

        if ($this->parent === null) {
            $out = null;
        } elseif ($this->parent instanceof $class_name) {
            $out = $this->parent;
        } else {
            $out = $this->parent->getFirstAncestor($class_name);
        }

        return $out;
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects needed by this UI object
     *
     * If this UI object is not visible, an empty set is returned to reduce
     * the number of required HTTP requests.
     *
     * @return Html\ResourceSet the Html\Resource objects needed by this UI
     *                          object.
     */
    public function getHtmlHeadEntrySet()
    {
        if ($this->isVisible()) {
            $set = new Html\ResourceSet($this->html_head_entry_set);
        } else {
            $set = new Html\ResourceSet();
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the Html\Resource objects that MAY needed by this UI object
     *
     * Even if this object is not displayed, all the resources that may be
     * required to display it are returned.
     *
     * @return Html\ResourceSet the Html\Resource objects that MAY be needed by
     *                          this UI object.
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        return new Html\ResourceSet($this->html_head_entry_set);
    }

    // }}}
    // {{{ public function isVisible()

    /**
     * Gets whether or not this UI object is visible
     *
     * Looks at the visible property of the ancestors of this UI object to
     * determine if this UI object is visible.
     *
     * @return boolean true if this UI object is visible and false if it is not.
     *
     * @see Object::$visible
     */
    public function isVisible()
    {
        if ($this->parent instanceof Object) {
            return ($this->parent->isVisible() && $this->visible);
        } else {
            return $this->visible;
        }
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * To perform a shallow copy, use PHP's clone keyword.
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                          objects in the UI tree. This can be used to
     *                          ensure object ids are unique for a copied UI
     *                          tree. If not specified, UI objects in the
     *                          returned copy will have identical ids to the
     *                          original tree. This can cause problems if both
     *                          the original and copy are displayed during the
     *                          same request.
     *
     * @return Object a deep copy of the UI tree starting with this UI object.
     *                The returned UI object does not have a parent and can be
     *                inserted into another UI tree.
     */
    public function copy($id_suffix = '')
    {
        $copy = clone $this;
        $copy->parent = null;
        return $copy;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this user-interface
     * object
     *
     * User-interface objects aggregate the list of user-specified classes and
     * may add static CSS classes of their own in this method.
     *
     * @return array the array of CSS classes that are applied to this
     *               user-interface object.
     *
     * @see Object::getCSSClassString()
     */
    protected function getCSSClassNames()
    {
        return $this->classes;
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets inline JavaScript used by this user-interface object
     *
     * @return string inline JavaScript used by this user-interface object.
     */
    protected function getInlineJavaScript()
    {
        return '';
    }

    // }}}
    // {{{ final protected function getCSSClassString()

    /**
     * Gets the string representation of this user-interface object's list of
     * CSS classes
     *
     * @return string the string representation of the CSS classes that are
     *                applied to this user-interface object. If this object
     *                has no CSS classes, null is returned rather than a blank
     *                string.
     *
     * @see Object::getCSSClassNames()
     */
    final protected function getCSSClassString()
    {
        $class_string = null;

        $class_names = $this->getCSSClassNames();
        if (count($class_names) > 0) {
            $class_string = implode(' ', $class_names);
        }

        return $class_string;
    }

    // }}}
    // {{{ final protected function getUniqueId()

    /**
     * Generates a unique id for this UI object
     *
     * Gets a unique id that may be used for the id property of this UI object.
     * Each time this method id called, a new unique identifier is generated so
     * you should only call this method once and set it to a property of this
     * object.
     *
     * @return string a unique identifier for this UI object.
     */
    final protected function getUniqueId()
    {
        // Because this method is not static, this counter will start at zero
        // for each class.
        static $counter = 0;

        $counter++;

        return get_class($this) . $counter;
    }

    // }}}
}
