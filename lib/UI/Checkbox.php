<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Model;

/**
 * A checkbox entry widget
 *
 * @package   Swat
 * @copyright 2004-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Checkbox extends InputControl implements Model\State
{
    // {{{ public properties

    /**
     * Checkbox value
     *
     * The state of the widget.
     *
     * @var boolean
     */
    public $value = false;

    /**
     * Access key
     *
     * Access key for this checkbox input, for keyboard nagivation.
     *
     * @var string
     */
    public $access_key = null;

    /**
     * The ordinal tab index position of the XHTML input tag
     *
     * Values 1 or greater will affect the tab index of this widget. A value
     * of 0 or null will use the position of the input tag in the XHTML
     * character stream to determine tab order.
     *
     * @var integer
     */
    public $tab_index;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new checkbox
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->requires_id = true;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this checkbox
     *
     * Outputs an appropriate XHTML tag.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        $this->getForm()->addHiddenField($this->id . '_submitted', 1);

        $input_tag = new Html\Tag('input');
        $input_tag->type = 'checkbox';
        $input_tag->class = $this->getCSSClassString();
        $input_tag->name = $this->id;
        $input_tag->id = $this->id;
        $input_tag->value = '1';
        $input_tag->accesskey = $this->access_key;
        $input_tag->tabindex = $this->tab_index;

        if ($this->value)
            $input_tag->checked = 'checked';

        if (!$this->isSensitive())
            $input_tag->disabled = 'disabled';

        echo '<span class="swat-checkbox-wrapper">';
        $input_tag->display();
        echo '<span class="swat-checkbox-shim"></span>';
        echo '</span>';
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this checkbox
     *
     * Sets the internal value of this checkbox based on submitted form data.
     */
    public function process()
    {
        parent::process();

        $id = $this->id . '_submitted';
        if ($this->getForm()->getHiddenField($id) === null) {
            return;
        }

        $data = &$this->getForm()->getFormData();
        $this->value = array_key_exists($this->id, $data);
    }

    // }}}
    // {{{ public function getState()

    /**
     * Gets the current state of this checkbox
     *
     * @return boolean the current state of this checkbox.
     *
     * @see Model\State::getState()
     */
    public function getState()
    {
        return $this->value;
    }

    // }}}
    // {{{ public function setState()

    /**
     * Sets the current state of this checkbox
     *
     * @param boolean $state the new state of this checkbox.
     *
     * @see Model\State::setState()
     */
    public function setState($state)
    {
        $this->value = $state;
    }

    // }}}
    // {{{ public function getFocusableHtmlId()

    /**
     * Gets the id attribute of the XHTML element displayed by this widget
     * that should receive focus
     *
     * @return string the id attribute of the XHTML element displayed by this
     *                widget that should receive focus or null if there is
     *                no such element.
     *
     * @see Widget::getFocusableHtmlId()
     */
    public function getFocusableHtmlId()
    {
        return ($this->visible) ? $this->id : null;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this checkbox
     *
     * @return array the array of CSS classes that are applied to this
     *               checkbox.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-checkbox');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
