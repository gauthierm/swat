<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;

/**
 * A radio list selection widget
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioList extends Flydown
{
    // {{{ private properties

    /**
     * Used for displaying radio buttons
     *
     * @var Html\Tag
     */
    private $input_tag;

    /**
     * Used for displaying radio button labels
     *
     * @var Html\Tag
     */
    private $label_tag;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new radiolist
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->show_blank  = false;
        $this->requires_id = true;

        $this->addStyleSheet('packages/swat/styles/swat-radio-list.css');
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this radio list
     */
    public function display()
    {
        $options = $this->getOptions();

        if (!$this->visible || $options === null)
            return;

        Widget::display();

        // add a hidden field so we can check if this list was submitted on
        // the process step
        $this->getForm()->addHiddenField($this->id.'_submitted', 1);

        if (count($options) == 1) {
            // get first and only element
            $this->displaySingle(current($options));
            return;
        }

        $ul_tag = new Html\Tag('ul');
        $ul_tag->id = $this->id;
        $ul_tag->class = $this->getCSSClassString();
        $ul_tag->open();

        $li_tag = new Html\Tag('li');
        $index = 0;

        foreach ($options as $option) {

            // add option-specific CSS classes from option metadata
            $classes = $this->getOptionMetadata($option, 'classes');
            if (is_array($classes)) {
                $li_tag->class = implode(' ', $classes);
            } elseif ($classes) {
                $li_tag->class = strval($classes);
            } else {
                $li_tag->removeAttribute('class');
            }

            $sensitive = $this->getOptionMetadata($option, 'sensitive');
            if ($sensitive === false || !$this->isSensitive()) {
                if ($li_tag->class === null) {
                    $li_tag->class = 'swat-insensitive';
                } else {
                    $li_tag->class.= ' swat-insensitive';
                }
            }

            if ($option instanceof Model\FlydownDivider) {
                if ($li_tag->class === null) {
                    $li_tag->class = 'swat-radio-list-divider-li';
                } else {
                    $li_tag->class.= ' swat-radio-list-divider-li';
                }
            }

            $li_tag->id = $this->id.'_li_'.(string)$index;
            $li_tag->open();

            if ($option instanceof Model\FlydownDivider) {
                $this->displayDivider($option, $index);
            } else {
                $this->displayOption($option, $index);
                $this->displayOptionLabel($option, $index);
            }

            $li_tag->close();
            $index++;
        }

        $ul_tag->close();
    }

    // }}}
    // {{{ protected function processValue()

    /**
     * Processes the value of this radio list from user-submitted form data
     *
     * @return boolean true if the value was processed from form data
     */
    protected function processValue()
    {
        $form = $this->getForm();

        if ($form->getHiddenField($this->id.'_submitted') === null)
            return false;

        $data = &$form->getFormData();
        $salt = $form->getSalt();

        if (isset($data[$this->id])) {
            if ($this->serialize_values) {
                $this->value = Util\String::signedUnserialize(
                    $data[$this->id],
                    $salt
                );
            } else {
                $this->value = $data[$this->id];
            }
        } else {
            $this->value = null;
        }

        return true;
    }

    // }}}
    // {{{ protected function displayDivider()

    /**
     * Displays a divider option in this radio list
     *
     * @param Model\Option $option the divider option to display.
     * @param integer      $index  the numeric index of the option in this
     *                             list. Starts at 0.
     */
    protected function displayDivider(Model\Option $option, $index)
    {
        $span_tag = new Html\Tag('span');
        $span_tag->class = 'swat-radio-list-divider';
        if ($option->value !== null)
            $span_tag->id = $this->id.'_'.(string)$option->value;

        $span_tag->setContent($option->title, $option->content_type);
        $span_tag->display();
    }

    // }}}
    // {{{ protected function displayOption()

    /**
     * Displays an option in the radio list
     *
     * @param Model\Option $option the option to display.
     * @param integer      $index  the numeric index of the option in this
     *                             list. Starts at 0.
     */
    protected function displayOption(Model\Option $option, $index)
    {
        if ($this->input_tag === null) {
            $this->input_tag = new Html\Tag('input');
            $this->input_tag->type = 'radio';
            $this->input_tag->name = $this->id;
        }

        $sensitive = $this->getOptionMetadata($option, 'sensitive');
        if ($sensitive === false || !$this->isSensitive()) {
            $this->input_tag->disabled = 'disabled';
        } else {
            $this->input_tag->removeAttribute('disabled');
        }

        if ($this->serialize_values) {
            $salt = $this->getForm()->getSalt();
            $this->input_tag->value = Util\String::signedSerialize(
                $option->value,
                $salt
            );
        } else {
            $this->input_tag->value = (string)$option->value;
        }

        $this->input_tag->removeAttribute('checked');

        // TODO: come up with a better system to set ids. This may  not be
        // unique and may also not be valid XHTML
        $this->input_tag->id = $this->id.'_'.(string)$option->value;

        if ($this->serialize_values) {
            if ($option->value === $this->value)
                $this->input_tag->checked = 'checked';
        } else {
            if ((string)$option->value === (string)$this->value)
                $this->input_tag->checked = 'checked';
        }

        echo '<span class="swat-radio-wrapper">';
        $this->input_tag->display();
        echo '<span class="swat-radio-shim"></span>';
        echo '</span>';
    }

    // }}}
    // {{{ protected function displayOptionLabel()

    /**
     * Displays an option in the radio list
     *
     * @param Model\Option $option the option for which to display the label.
     * @param integer      $index  the numeric index of the option in this
     *                             list. Starts at 0.
     */
    protected function displayOptionLabel(Model\Option $option, $index)
    {
        if ($this->label_tag === null) {
            $this->label_tag = new Html\Tag('label');
            $this->label_tag->class = 'swat-control';
        }

        $this->label_tag->for = $this->id.'_'.(string)$option->value;
        $this->label_tag->setContent($option->title, $option->content_type);
        $this->label_tag->display();
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this radio list
     *
     * @return array the array of CSS classes that are applied to this radio
     *               list.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-radio-list');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
