<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Database field
 *
 * Data class to represent a database field, a (name, type) pair.
 *
 * @package   SwatDB
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBField
{
    // {{{ public properties

    /**
     * The name of the database field
     *
     * @var string
     */
    public $name;

    /**
     * The type of the database field
     *
     * Any standard MDB2 datatype is valid here.
     *
     * @var string
     */
    public $type;

    // }}}
    // {{{ public function __construct()

    /**
     * @param string $field        a string representation of a database field
     *                             of the form <i>type:name</i> where
     *                             <i>name</i> is the name of the database
     *                             field and <i>type</i> is any standard MDB2
     *                             data type.
     * @param string $default_type optional. The type to use by default if it
     *                             is not specified in the <i>$field</i>
     *                             string. Any standard MDB2 data type is valid
     *                             here. Defaults to 'text'.
     */
    public function __construct($field, $default_type = 'text')
    {
        $x = explode(':', $field);

        if (isset($x[1])) {
            $this->name = $x[1];
            $this->type = $x[0];
        } else {
            $this->name = $x[0];
            $this->type = $default_type;
        }
    }

    // }}}
    // {{{ public function __toString()

    /**
     * Get the field as a string
     *
     * @return string a string representation of this database field in the
     *                form <i>type:name</i> where <i>name</i> is the name of
     *                the database field and <i>type</i> is a standard MDB2
     *                data type.
     */
    public function __toString()
    {
        return $this->type.':'.$this->name;
    }

    // }}}
}

?>
