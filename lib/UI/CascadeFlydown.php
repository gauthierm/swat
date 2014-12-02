<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Model;
use Silverorange\Swat\Util;
use Silverorange\Swat\L;

/**
 * A cascading flydown (aka combo-box) selection widget
 *
 * The term cascading refers to the fact that this flydown's contents are
 * updated dynamically based on the selected value of another flydown.
 *
 * The value of the other Flydown cascades to this CascadeFlydown.
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class CascadeFlydown extends Flydown
{
    // {{{ public properties

    /**
     * Flydown options
     *
     * An array of parents and {@link Model\Option}s for the flydown. Each
     * parent value is associated to an array of possible child values, in the
     * form:
     *
     * <code>
     * array(
     *     parent_value1 => array(Model\Option1, Model\Option2),
     *     parent_value2 => array(Model\Option3, Model\Option4),
     * );
     * </code>
     *
     * @var array
     */
    public $options = array();

    /**
     * Cascade from
     *
     * A reference to the {@link Widget} that this item cascades from.
     *
     * @var Widget
     */
    public $cascade_from = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new calendar
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $yui = new Html\YUI(array('event'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addJavaScript('packages/swat/javascript/swat-cascade.js');
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this cascading flydown
     *
     * {@link Flydown::$show_blank} is set to false here.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        Util\JavaScript::displayInline($this->getInlineJavaScript());
    }

    // }}}
    // {{{ public function addOption()

    /**
     * Adds an option to this option control
     *
     * @param mixed              $parent       the value of the parent value
     *                                         from which this option cascades.
     * @param mixed|Model\Option $value        either a value for the option,
     *                                         or a {@link Model\Option} object.
     *                                         If an option object is used, the
     *                                         <i>$title</i> and
     *                                         <i>$content_type</i> parameters
     *                                         of this method are ignored.
     * @param string             $title        the title of the added option.
     *                                         Ignored if the <i>$value</i>
     *                                         parameter is an option object.
     * @param string             $content_type optional. The content type of the
     *                                         title. If not specified, defaults
     *                                         to 'text/plain'. Ignored if the
     *                                         <i>$value</i> parameter is an
     *                                         option object.
     */
    public function addOption($parent, $value, $title = '',
        $content_type = 'text/plain')
    {
        if ($value instanceof Model\Option) {
            $option = $value;
        } else {
            $option = new Model\Option($value, $title, $content_type);
        }

        $this->options[$parent][] = $option;
    }

    // }}}
    // {{{ public function addOptionsByArray()

    /**
     * Adds options to this option control using an associative array
     *
     * @param array  $options      an array of options. Keys are option parent
     *                             values. Values are a 2-element associative
     *                             array of title => value pairs.
     * @param string $content_type optional. The content type of the option
     *                             titles. If not specified, defaults to
     *                             'text/plain'.
     */
    public function addOptionsByArray(array $options,
        $content_type = 'text/plain')
    {
        foreach ($options as $parent => $child_options) {
            foreach ($child_options as $value => $title)
                $this->addOption($parent, $value, $title, $content_type);
        }
    }

    // }}}
    // {{{ protected function getOptions()

    /**
     * Gets the options of this flydown as a flat array
     *
     * For the cascading flydown, the array returned
     *
     * The array is of the form:
     *    value => title
     *
     * @return array the options of this flydown as a flat array.
     *
     * @see Flydown::getOptions()
     */
    protected function &getOptions()
    {
        $options = array();

        // If the parent flydown is empty, set parent_value to a blank string.
        // This will then return any options that exist for a blank parent.
        $parent_value = ($this->hasEmptyParent()) ?
            '' : $this->getParentValue();

        // parent_value is null when the parent has multiple options
        if ($parent_value === null) {
            if ($this->cascade_from->show_blank) {
                // select the blank option on the cascade from
                $options = array(
                    new Model\Option('', '&nbsp;'),
                    new Model\Option('', '&nbsp;'),
                );
            } else {
                // select the first option on the cascade from
                $first_value = reset($this->cascade_from->getOptions())->value;
                $options = $this->options[$first_value];
            }
        } elseif (isset($this->options[$parent_value])) {
            $options = $this->options[$parent_value];
        } else {
            // if the options array doesn't exist for this parent_value, then
            // assume that means we don't want any values in this flydown for
            // that option.
            $options = array(new Model\Option(null, null));
        }

        return $options;
    }

    // }}}
    // {{{ protected function getParentValue()

    protected function getParentValue()
    {
        return ($this->cascade_from instanceof Flydown) ?
            $this->cascade_from->value : null;
    }

    // }}}
    // {{{ protected function getParentOptions()

    protected function getParentOptions()
    {
        return ($this->cascade_from instanceof Flydown) ?
            $this->cascade_from->getOptions() : array();
    }

    // }}}
    // {{{ protected function hasEmptyParent()

    protected function hasEmptyParent()
    {
        return (count($this->getParentOptions()) === 0);
    }

    // }}}
    // {{{ protected function hasSingleParent()

    protected function hasSingleParent()
    {
        return ((count($this->getParentOptions()) === 1) &&
            $this->cascade_from->show_blank == false);
    }

    // }}}
    // {{{ protected function getBlankOption()

    protected function getBlankOption()
    {
        $blank_title = ($this->blank_title === null)
            ? L::_('choose one ...')
            : $this->blank_title;

        return new FlydownBlankOption(null, $blank_title);
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets the inline JavaScript that makes this control work
     *
     * @return string the inline JavaScript that makes this control work.
     */
    protected function getInlineJavaScript()
    {
        // Javascript is unnecessary when the parent flydown is empty, or when
        // it has a single value as in both cases the parent value never
        // changes.
        if ($this->hasEmptyParent() ||
            $this->hasSingleParent()) {
            return;
        }

        $javascript = sprintf("var %s_cascade = new SwatCascade('%s', '%s');",
            $this->id,
            $this->cascade_from->id,
            $this->id);

        $salt = $this->getForm()->getSalt();

        $flydown_value = ($this->serialize_values) ?
            $this->value : (string)$this->value;

        foreach ($this->options as $parent => $options) {
            if ($this->cascade_from->serialize_values) {
                $parent = Util\String::signedSerialize($parent, $salt);
            }

            if ($this->show_blank && count($options) > 0) {
                if ($this->serialize_values) {
                    $value = Util\String::signedSerialize(null, $salt);
                } else {
                    $value = '';
                }

                $blank_title = ($this->blank_title === null)
                    ? L::_('choose one ...')
                    : $this->blank_title;

                $javascript.= sprintf(
                    "\n%s_cascade.addChild(%s, %s, %s);",
                    $this->id,
                    Util\JavaScript::quoteString($parent),
                    Util\JavaScript::quoteString($value),
                    Util\JavaScript::quoteString($blank_title)
                );
            }

            foreach ($options as $option) {
                if ($this->serialize_values) {
                    // if they are serialized, we want to compare the actual
                    // values
                    $selected = ($flydown_value === $option->value) ?
                        'true' : 'false';

                    $value = Util\String::signedSerialize(
                        $option->value,
                        $salt
                    );
                } else {
                    // if they are not serialized, we want to compare the string
                    // value
                    $value = (string)$option->value;

                    $selected = ($flydown_value === $value) ?
                        'true' : 'false';
                }

                $javascript.= sprintf(
                    "\n%s_cascade.addChild(%s, %s, %s, %s);",
                    $this->id,
                    Util\JavaScript::quoteString($parent),
                    Util\JavaScript::quoteString($value),
                    Util\JavaScript::quoteString($option->title),
                    $selected
                );
            }
        }

        $javascript.= sprintf("\n%s_cascade.init();",
            $this->id);

        return $javascript;
    }

    // }}}
}

?>
