<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\I18N;
use Silverorange\Swat\L;


/**
 * An input control for entering a delimited list of data
 *
 * @package   Swat
 * @copyright 2006-2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ListEntry extends Entry
{
    // {{{ public properties

    /**
     * The values of this list entry
     *
     * @var array
     */
    public $values = array();

    /**
     * The delimiter for entries in this list entry
     *
     * This may be a single character or a string of characters. The delimiter
     * is used to separate list entries. Entries in the
     * {@link ListEntry::$values} array do not include the delimiter.
     *
     * By default, the delimiter is a comma.
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * Whether or not to trim whitespace from values between delimiters
     *
     * If this is true, whitespace before or after the delimiter is removed
     * from entries in the array of values. If this is false, whitespace is
     * maintained.
     *
     * For example, if the user enters 'orange, apple' in form
     * data and {ListEntry::$trim_whitespace} is true, the second entry in
     * {@link ListEntry::$values} array will be 'apple'. In the same example
     * if {@link ListEntry::$trim_whitespace} is false, the second entry in the
     * array will be ' apple'.
     *
     * @var boolean
     */
    public $trim_whitespace = true;

    /**
     * The maximum number of allowed entries in this list entry
     *
     * If this value is set to null or 0 then there is no maximum number of
     * allowed entries.
     *
     * @var integer
     */
    public $max_entries = null;

    /**
     * The minimum number of required entries in this list entry
     *
     * If {@link InputControl::$required} is set to true for this list
     * entry this property specifies the minimum number of entries the user
     * must enter.
     *
     * Note: If {@link InputControl::$required} is set to false, this property
     * has no effect.
     *
     * @var integer
     * @see InputControl::$required
     */
    public $min_entries = 1;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new list entry widget
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->minlength = 1;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this list entry
     */
    public function display()
    {
        if (!$this->visible)
            return;

        // Do not have a maxlength on the XHTML input tag. This relies on
        // internal knowledge of the parent::display() method.
        $old_maxlength = $this->maxlength;
        $this->maxlength = null;

        parent::display();

        $this->maxlength = $old_maxlength;
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this list entry widget
     *
     * The user entered values are split into an array of values and stored in
     * the {@link ListEntry::$values} array.
     */
    public function process()
    {
        $data = &$this->getForm()->getFormData();

        if (!isset($data[$this->id])) {
            $this->value = null;
        } elseif ($data[$this->id] == '') {
            $this->value = null;
        } else {
            $this->value = $data[$this->id];
        }

        $this->values = $this->splitValues($this->value);
        $locale = I18N\Locale::get();

        if (!$this->required && count($this->values) == 0) {
            return;

        } elseif ($this->max_entries > 0 &&
            count($this->values) > $this->max_entries) {

            $message = sprintf(
                L::_('The %%s field cannot have more than %s entries.'),
                $locale->formatNumber($this->max_entries)
            );
            $this->addMessage(new Model\Message($message, 'error'));

        } elseif ($this->required &&
            count($this->values) < $this->min_entries) {

            $message = sprintf(
                L::ngettext(
                    'The %%s field must have at least %s entry.',
                    'The %%s field must have at least %s entries.',
                    $this->min_entries
                ),
                $locale->formatNumber($this->min_entries)
            );
            $this->addMessage(new Model\Message($message, 'error'));
        }

        // validate individual values

        $min_length_msg = null;
        $max_length_msg = null;
        $min_length_error_values = array();
        $max_length_error_values = array();

        foreach ($this->values as $value) {
            $len = strlen($value);
            if ($this->maxlength !== null && $len > $this->maxlength) {
                $max_length_msg = sprintf(
                    L::ngettext(
                        'Entries in the %%s field must be less than %s ' .
                        'character long.',
                        'Entries in the %%s field must be less than %s ' .
                        'characters long.',
                        $this->maxlength
                    ),
                    $locale->formatNumber($this->maxlength)
                ) . ' ';

                $max_length_error_values[] = $value;

            } elseif ($this->minlength !== null && $len < $this->minlength) {
                $min_length_msg = sprintf(
                    L::ngettext(
                        'Entries in the %%s field must be at least %s ' .
                        'character long.',
                        'Entries in the %%s field must be at least %s ' .
                        'characters long.',
                        $this->minlength
                    ),
                    $locale->formatNumber($this->minlength)
                ) . ' ';

                $min_length_error_values[] = $value;
            }
        }

        if ($min_length_msg !== null) {
            $min_length_msg .= sprintf(
                L::ngettext(
                    'The following entry is too short: %s.',
                    'The following entries are too short: %s.',
                    count($min_length_error_values)
                ),
                implode(', ', $min_length_error_values),
                $locale->formatNumber(count($min_length_error_values))
            );
            $this->addMessage(new Model\Message($min_length_msg, 'error'));
        }

        if ($max_length_msg !== null) {
            $max_length_msg .= sprintf(
                L::ngettext(
                    'The following entry is too long: %s.',
                    'The following entries are too long: %s.',
                    count($max_length_error_values)
                ),
                implode(', ', $max_length_error_values),
                $locale->formatNumber(count($max_length_error_values))
            );
            $this->addMessage(new Model\Message($max_length_msg, 'error'));
        }
    }

    // }}}
    // {{{ public function getState()

    /**
     * Gets the current state of this entry widget
     *
     * @return string the current state of this entry widget.
     *
     * @see Model\State::getState()
     */
    public function getState()
    {
        return $this->values;
    }

    // }}}
    // {{{ public function setState()

    /**
     * Sets the current state of this list entry widget
     *
     * @param string $state the new state of this list entry widget.
     *
     * @see Model\State::setState()
     */
    public function setState($values)
    {
        if (is_array($values)) {
            $this->values = $values;
        } else {
            $this->values = $this->splitValues($values);
        }
    }

    // }}}
    // {{{ public function getDisplayValue()

    /**
     * Gets the value displayed in the XHTML input
     *
     * For list entry, this is a delimiter separated string containing the
     * elements of {@link ListEntry::$values}.
     *
     * @param array $value the value to format for display.
     *
     * @return string the values displayed in the XHTML input.
     */
    protected function getDisplayValue($value)
    {
        if ($this->trim_whitespace && $this->delimiter != ' ') {
            return implode($this->delimiter . ' ', $this->values);
        } else {
            return implode($this->delimiter, $this->values);
        }
    }

    // }}}
    // {{{ public function getNote()

    /**
     * Gets a note describing the rules on this list entry
     *
     * This note informs the user what numbers of entries are valid for this
     * list entry. This note does not mention anything about what type of
     * delimiter is used. Developers are responsible for ensuring that users
     * are notified what type of delimiters are used.
     *
     * @return Model\Message an informative note of how to use this list entry.
     *
     * @see Control::getNote()
     */
    public function getNote()
    {
        $message = null;
        $locale = I18N\Locale::get();

        if ($this->max_entries !== null && $this->max_entries > 0 &&
            $this->min_entries === null) {

            $message = new Model\Message(
                sprintf(
                    L::ngettext(
                        'List can contain at most %s entry',
                        'List can contain at most %s entries',
                        $this->max_entries
                    ),
                    $locale->formatNumber($this->max_entries)
                )
            );
        } elseif (($this->max_entries === null  ||
            $this->max_entries == 0) &&
            $this->min_entries > 1 && $this->required == true) {

            $message = new Model\Message(
                sprintf(
                    L::ngettext(
                        'List must contain at least %s entry',
                        'List must contain at least %s entries',
                        $this->min_entries
                    ),
                    $locale->formatNumber($this->min_entries)
                )
            );
        } elseif ($this->max_entries !== null && $this->max_entries > 0 &&
            $this->min_entries !== null && $this->required == true) {

            $message = new Model\Message(
                sprintf(
                    L::_('List must contain between %s and %s entries.'),
                    $locale->formatNumber($this->min_entries),
                    $locale->formatNumber($this->max_entries)
                )
            );
        }

        return $message;
    }

    // }}}
    // {{{ protected function splitValues()

    /**
     * Splits a value string with entries separated by delimiters into
     * an array
     *
     * If {@link ListEntry::$trim_whitespace} is set to true, whitespace is not
     * included in the split values.
     *
     * @param string $value the string to split.
     *
     * @return array the string of delimiter separated values split into an
     *               array of values.
     */
    protected function splitValues($value)
    {
        $delimiter = preg_quote($this->delimiter, '/');

        if ($this->trim_whitespace) {
            $expression = '/\s*' . $delimiter . '\s*/u';
        } else {
            $expression = '/' . $delimiter . '/u';
        }

        return preg_split($expression, $value, -1, PREG_SPLIT_NO_EMPTY);
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this entry
     *
     * @return array the array of CSS classes that are applied to this
     *               entry.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-list-entry');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
