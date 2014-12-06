<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\Data;

use Silverorange\Swat\Exception;

/**
 * Maps class names to overridden class names
 *
 * This is a static class and should not be instantiated.
 *
 * This class also attempts to require class-definition files for mapped
 * class names. You can modify the behaviour of automatic class-definition
 * file requiring using the {@link ClassMap::addPath()} and
 * {@link ClassMap::removePath()} methods.
 *
 * @package   SwatDB
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ClassMap
{
    // {{{ private properties

    /**
     * An associative array of class-mappings
     *
     * The array has keys of the 'from class name' and values of the
     * 'to class name'.
     *
     * @var array
     */
    private static $map = array();

    /**
     * Paths to search for class-definition files
     *
     * @var array
     */
    private static $search_paths = array('.');

    // }}}
    // {{{ public static function add()

    /**
     * Maps a class name to another class name
     *
     * Subsequent calls to {@link ClassMap::get()} using the
     * <i>$from_class_name</i> will return the <i>$to_class_name</i>. Class
     * names may be mapped to already mapped class names. For example:
     *
     * <code>
     * ClassMap::add('Object', 'MyObject');
     * ClassMap::add('MyObject', 'MyOtherObject');
     * echo ClassMap::get('Object'); // MyOtherObject
     * </code>
     *
     * If a circular dependency is created, an exception is thrown. If the
     * <i>$from_class_name</i> is already mapped to another class the old
     * mapping is overwritten.
     *
     * @param string $from_class_name the class name to map from.
     * @param string $to_class_name   the class name to map to. The mapped
     *                                class must be a subclass of the
     *                                <i>$from_class_name</i> otherwise class
     *                                resolution using
     *                                {@link ClassMap::get()} will throw
     *                                an exception.
     *
     * @throws Exception\Exception if the added mapping creates a circular
     *         dependency.
     */
    public static function add($from_class_name, $to_class_name)
    {
        // check for circular dependency
        if (array_key_exists($to_class_name, self::$map)) {
            $class_name = $to_class_name;
            $child_class_names = array($class_name);
            while (array_key_exists($class_name, self::$map)) {
                $class_name = self::$map[$class_name];
                $child_class_names[] = $class_name;
            }

            if (in_array($from_class_name, $child_class_names)) {
                throw new Exception\Exception(
                    sprintf(
                        'Circular class dependency detected: %s => %s',
                        $from_class_name,
                        implode(' => ', $child_class_names)
                    )
                );
            }
        }

        self::$map[$from_class_name] = $to_class_name;
    }

    // }}}
    // {{{ public static function get()

    /**
     * Resolves a class name from the class map
     *
     * @param string $from_class_name the name of the class to resolve.
     *
     * @return string the resolved class name. If no class mapping exists for
     *                for the given class name, the same class name is
     *                returned.
     *
     * @throws Exception\InvalidClassException if a mapped class is not a
     *         subclass of its original class.
     */
    public static function get($from_class_name)
    {
        $include_paths = explode(':', get_include_path());

        $to_class_name = $from_class_name;

        while (array_key_exists($from_class_name, self::$map)) {
            $to_class_name = self::$map[$from_class_name];

            // try to load class definition for $to_class_name
            if (!class_exists($to_class_name) &&
                count(self::$search_paths) > 0) {
                foreach (self::$search_paths as $search_path) {
                    // check if search path is relative
                    if ($search_path[0] == '/') {
                        $filename = sprintf(
                            '%s/%s.php',
                            $search_path,
                            $to_class_name
                        );
                        if (file_exists($filename)) {
                            require_once $filename;
                            break;
                        }
                    } else {
                        foreach ($include_paths as $include_path) {
                            $filename = sprintf(
                                '%s/%s/%s.php',
                                $include_path,
                                $search_path,
                                $to_class_name
                            );
                            if (file_exists($filename)) {
                                require_once $filename;
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!is_subclass_of($to_class_name, $from_class_name)) {
                throw new Exception\InvalidClassException(
                    sprintf(
                        'Invalid class-mapping detected. %s is not a ' .
                        'subclass of %s.',
                        $to_class_name,
                        $from_class_name
                    )
                );
            }

            $from_class_name = $to_class_name;
        }

        return $to_class_name;
    }

    // }}}
    // {{{ public static function addPath()

    /**
     * Adds a search path for class-definition files
     *
     * When an undefined class is resolved, the class map attempts to find
     * and require a class-definition file for the class.
     *
     * All search paths are relative to the PHP include path. The empty search
     * path ('.') is included by default.
     *
     * @param string $search_path the path to search for class-definition files.
     *
     * @see ClassMap::removePath()
     */
    public static function addPath($search_path)
    {
        if (!in_array($search_path, self::$search_paths, true)) {
            // add path to front of array since it is more likely we will find
            // class-definitions in manually added search paths
            array_unshift(self::$search_paths, $search_path);
        }
    }

    // }}}
    // {{{ public static function removePath()

    /**
     * Removes a search path for class-definition files
     *
     * @param string $search_path the path to remove.
     *
     * @see ClassMap::addPath()
     */
    public static function removePath($search_path)
    {
        $index = array_search($search_path, self::$search_paths);
        if ($index !== false) {
            array_splice(self::$search_paths, $index, 1);
        }
    }

    // }}}
    // {{{ private function __construct()

    /**
     * The class map is a static object and should not be instantiated
     */
    private function __construct()
    {
    }

    // }}}
}
