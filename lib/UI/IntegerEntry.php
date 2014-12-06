<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Model;
use Silverorange\Swat\I18N;
use Silverorange\Swat\L;

/**
 * An integer entry widget
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class IntegerEntry extends NumericEntry
{
    // {{{ public function process()

    /**
     * Checks to make sure value is an integer
     *
     * If the value of this widget is not an integer then an error message is
     * attached to this widget.
     */
    public function process()
    {
        parent::process();

        if ($this->value === null)
            return;

        try {
            $integer_value = $this->getNumericValue($this->value);

            if ($integer_value === null)
                $this->addMessage($this->getValidationMessage('integer'));
            else
                $this->value = $integer_value;

        } catch (Exception\IntegerOverflowException $e) {
            if ($e->getSign() > 0)
                $this->addMessage($this->getValidationMessage(
                    'integer-maximum'));
            else
                $this->addMessage($this->getValidationMessage(
                    'integer-minimum'));

            $integer_value = null;
        }
    }

    // }}}
    // {{{ protected function getDisplayValue()

    /**
     * Formats an integer value to display
     *
     * @param string $value the value to format for display.
     *
     * @return string the formatted value.
     */
    protected function getDisplayValue($value)
    {
        if (is_int($value)) {
            $locale = I18N\Locale::get();
            $thousands_separator =
                ($this->show_thousands_separator) ? null : '';

            $value = $locale->formatNumber($value, 0,
                array('thousands_separator' => $thousands_separator));
        } else {
            $value = parent::getDisplayValue($value);
        }

        return $value;
    }

    // }}}
    // {{{  protected function getNumericValue()

    /**
     * Gets the numeric value of this widget
     *
     * This allows each widget to parse raw values how they want to get numeric
     * values.
     *
     * @param string $value the raw value to use to get the numeric value.
     *
     * @return mixed the numeric value of this entry widget or null if no
     *               numeric value is available.
     */
    protected function getNumericValue($value)
    {
        $locale = I18N\Locale::get();
        return $locale->parseInteger($value);
    }

    // }}}
    // {{{ protected function getValidationMessage()

    /**
     * Gets a validation message for this integer entry
     *
     * @see Entry::getValidationMessage()
     * @param string $id the string identifier of the validation message.
     *
     * @return Model\Message the validation message.
     */
    protected function getValidationMessage($id)
    {
        switch ($id) {
            case 'integer':
                if ($this->minimum_value < 0) {
                    $text = $this->show_field_title_in_messages
                        ? L::_('The %s field must be an integer.')
                        : L::_('This field must be an integer.');
                } else {
                    $text = $this->show_field_title_in_messages
                        ? L::_('The %s field must be a whole number.')
                        : L::_('This field must be a whole number.');
                }
                $message = new Model\Message($text, 'error');
                break;

            case 'integer-maximum':
                $text = $this->show_field_title_in_messages
                    ? L::_('The %s field is too big.')
                    : L::_('This field is too big.');

                $message = new Model\Message($text, 'error');
                break;

            case 'integer-minimum':
                $text = $this->show_field_title_in_messages
                    ? L::_('The %s field is too small.')
                    : L::_('The this field is too small.');

                $message = new Model\Message($text, 'error');
                break;

            default:
                $message = parent::getValidationMessage($id);
                break;
        }

        return $message;
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
        $classes = array('swat-integer-entry');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
