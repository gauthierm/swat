<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Model;
use Silverorange\Swat\L;

/**
 * Base class for controls that accept user input on forms.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class InputControl extends Control
{
    // {{{ public properties

    /**
     * Whether this entry widget is required or not
     *
     * Must have a non-empty value when processed.
     *
     * @var boolean
     */
    public $required = false;

    /**
     * Whether to use the field title in validation messages
     *
     * @var boolean
     */
    public $show_field_title_in_messages = true;

    // }}}
    // {{{ public function init()

    /**
     * Initializes this widget
     *
     * Sets required property on the form field that contains this widget.
     *
     * @see Widget::init()
     */
    public function init()
    {
        parent::init();

        if ($this->required && $this->parent instanceof FormField) {
            $this->parent->required = true;
        }
    }

    // }}}
    // {{{ public function getForm()

    /**
     * Gets the form that this control is contained in
     *
     * You can also get the parent form with the
     * {@link Object::getFirstAncestor()} method but this method is more
     * convenient and throws an exception .
     *
     * @return Form the form this control is in.
     *
     * @throws Exception\Exception
     */
    public function getForm()
    {
        $form = $this->getFirstAncestor('\Silverorange\Swat\UI\Form');
        if ($form === null) {
            $path = get_class($this);
            $object = $this->parent;
            while ($object !== null) {
                $path = get_class($object).'/'.$path;
                $object = $object->parent;
            }
            throw new Exception\Exception(
                "Input controls must reside inside a Form widget. UI-Object ".
                "path:\n".$path
            );
        }

        return $form;
    }

    // }}}
    // {{{ protected function getValidationMessage()

    /**
     * Gets a validation message for this control
     *
     * Can be used by sub-classes to change the validation messages.
     *
     * @param string $id the string identifier of the validation message.
     *
     * @return Model\Message the validation message.
     */
    protected function getValidationMessage($id)
    {
        switch ($id) {
        case 'required':
            $text = $this->show_field_title_in_messages
                ? L::_('%s is required.')
                : L::_('This field is required.');

            break;
        case 'too-long':
            $text = $this->show_field_title_in_messages
                ? L::_('The %%s field can be at most %s characters long.')
                : L::_('This field can be at most %s characters long.');

            break;
        default:
            $text = $this->show_field_title_in_messages
                ? L::_('There is a problem with the %s field.')
                : L::_('There is a problem with this field.');

            break;
        }

        $message = new Model\Message($text, 'error');
        return $message;
    }

    // }}}
}

?>
