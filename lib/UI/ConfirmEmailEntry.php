<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\L;

/**
 * An email address confirmation entry widget
 *
 * Automatically compares the value of the confirmation with the matching
 * email entry widget to see if they match.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ConfirmEmailEntry extends EmailEntry
{
    // {{{ public properties

    /**
     * A reference to the matching email entry widget
     *
     * @var EmailEntry
     */
    public $email_widget = null;

    // }}}
    // {{{ public function process()

    /**
     * Checks to make sure email addresses match
     *
     * Checks to make sure the values of the two email address fields are the
     * same. If an associated email entry widget is not set, an exception is
     * thrown. If the addresses do not match, an error is added to this widget.
     *
     * @throws Exception\Exception
     */
    public function process()
    {
        parent::process();

        if ($this->value === null)
            return;

        if ($this->email_widget === null) {
            throw new Exception\Exception(
                "Property 'email_widget' is null. Expected a reference to " .
                "an EmailEntry."
            );
        }

        if ($this->email_widget->value !== null) {
            if (strcmp($this->email_widget->value, $this->value) != 0) {
                $message = L::_(
                    'Email address and confirmation email address do not match.'
                );

                $this->addMessage(new Model\Message($message, 'error'));
            }
        }
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
        $classes = array('swat-confirm-email-entry');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
