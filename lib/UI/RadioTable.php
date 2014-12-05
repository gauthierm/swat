<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;

/**
 * Special radio-list that can display multi-line list items using a
 * tabular format
 *
 * @package   Swat
 * @copyright 2006-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioTable extends RadioList
{
    // {{{ public function __construct()

    /**
     * Creates a new radio table
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->addStyleSheet('packages/swat/styles/swat-radio-table.css');
    }

    // }}}
    // {{{ public function display()

    public function display()
    {
        $options = $this->getOptions();

        if (!$this->visible || $options === null) {
            return;
        }

        Widget::display();

        // add a hidden field so we can check if this list was submitted on
        // the process step
        $this->getForm()->addHiddenField($this->id . '_submitted', 1);

        if ($this->show_blank) {
            $options = array_merge(
                array(
                    new Model\Option(
                        null,
                        $this->blank_title
                    )
                ),
                $options
            );
        }

        $table_tag = new Html\Tag('table');
        $table_tag->id = $this->id;
        $table_tag->class = $this->getCSSClassString();
        $table_tag->open();

        foreach ($options as $index => $option) {
            $this->displayRadioTableOption($option, $index);
        }

        $table_tag->close();
    }

    // }}}
    // {{{ protected function displayRadioTableOption()

    /**
     * Displays a single option in this radio table
     *
     * @param Model\Option $option the option to display.
     * @param integer      $index  the numeric index of the option in this
     *                             list. Starts at 0.
     */
    protected function displayRadioTableOption(Model\Option $option, $index)
    {
        $tr_tag = $this->getTrTag($option, $index);

        // add option-specific CSS classes from option metadata
        $classes = $this->getOptionMetadata($option, 'classes');
        if (is_array($classes)) {
            $tr_tag->class = implode(' ', $classes);
        } elseif ($classes) {
            $tr_tag->class = strval($classes);
        }

        $tr_tag->open();

        if ($option instanceof Model\FlydownDivider) {
            echo '<td class="swat-radio-table-input">';
            echo '&nbsp;';
            echo '</td><td class="swat-radio-table-label">';
            $this->displayDivider($option, $index);
            echo '</td>';
        } else {
            echo '<td class="swat-radio-table-input">';
            $this->displayOption($option, $index);
            printf(
                '</td><td id="%s" class="swat-radio-table-label">',
                $this->id . '_' . (string)$option->value . '_label'
            );
            $this->displayOptionLabel($option, $index);
            echo '</td>';
        }

        $tr_tag->close();
    }

    // }}}
    // {{{ protected function getTrTag()

    /**
     * Gets the tr tag used to display a single option in this radio table
     *
     * @param Model\Option $option the option to display.
     * @param integer      $index  the numeric index of the option in this list.
     */
    protected function getTrTag(Model\Option $option, $index)
    {
        return new Html\Tag('tr');
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this radio table
     *
     * @return array the array of CSS classes that are applied to this radio
     *               table.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-radio-table');
        $classes = array_merge($classes, $this->classes);
        return $classes;
    }

    // }}}
}
