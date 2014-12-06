<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Util;
use Silverorange\Swat\L;

/**
 * Pop-up calendar widget
 *
 * This widget uses JavaScript to display a popup date selector. It is used
 * inside the {@link Util\DateEntry} widget but can be used by itself as well.
 *
 * @package   Swat
 * @copyright 2004-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Calendar extends Control
{
    // {{{ public properties

    /**
     * Start date of the valid range (inclusive).
     *
     * @var Util\Date
     */
    public $valid_range_start;

    /**
     * End date of the valid range (exclusive).
     *
     * @var Util\Date
     */
    public $valid_range_end;

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

        $yui = new Html\YUI(array('dom', 'container'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addStyleSheet('packages/swat/styles/swat-calendar.css');
        $this->addJavaScript('packages/swat/javascript/swat-calendar.js');
        $this->addJavaScript(
            'packages/swat/javascript/swat-z-index-manager.js'
        );
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this calendar widget
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        $container_div_tag = new Html\Tag('div');
        $container_div_tag->id = $this->id;
        $container_div_tag->class = $this->getCSSClassString();
        $container_div_tag->open();

        // toggle button content is displayed with JavaScript

        if ($this->valid_range_start === null) {
            $today = new Util\Date();
            $value = $today->formatLikeIntl('MM/dd/yyyy');
        } else {
            $value = $this->valid_range_start->formatLikeIntl('MM/dd/yyyy');
        }

        $input_tag = new Html\Tag('input');
        $input_tag->type = 'hidden';
        $input_tag->id = $this->id.'_value';
        $input_tag->name = $this->id.'_value';
        $input_tag->value = $value;
        $input_tag->display();

        $container_div_tag->close();

        Util\JavaScript::displayInline($this->getInlineJavaScript());
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this calendar widget
     *
     * @return array the array of CSS classes that are applied to this calendar
     *               widget.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-calendar');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets inline calendar JavaScript
     *
     * Inline JavaScript is the majority of the calendar code.
     */
    protected function getInlineJavaScript()
    {
        static $shown = false;

        if (!$shown) {
            $javascript = $this->getInlineJavaScriptTranslations();
            $shown = true;
        } else {
            $javascript = '';
        }

        if (isset($this->valid_range_start))
            $start_date = $this->valid_range_start->formatLikeIntl('MM/dd/yyyy');
        else
            $start_date = '';

        if (isset($this->valid_range_end)) {
            // JavaScript calendar is inclusive, subtract one second from range
            $tmp = clone $this->valid_range_end;
            $tmp->subtractSeconds(1);
            $end_date = $tmp->formatLikeIntl('MM/dd/yyyy');
        } else {
            $end_date = '';
        }

        $javascript.=
            sprintf("var %s_obj = new SwatCalendar('%s', '%s', '%s');",
            $this->id,
            $this->id,
            $start_date,
            $end_date);

        return $javascript;
    }

    // }}}
    // {{{ protected function getInlineJavaScriptTranslations()

    /**
     * Gets translatable string resources for the JavaScript object for
     * this widget
     *
     * @return string translatable JavaScript string resources for this widget.
     */
    protected function getInlineJavaScriptTranslations()
    {
        /*
         * This date is arbitrary and is just used for getting week and
         * month names.
         */
        $date = new Util\Date();
        $date->setDay(1);
        $date->setMonth(1);
        $date->setYear(1995);

        // Get the names of weeks (locale-specific)
        $week_names = array();
        for ($i = 1; $i < 8; $i++) {
            $week_names[] = $date->formatLikeIntl('EEE');
            $date->setDay($i + 1);
        }
        $week_names = "['".implode("', '", $week_names)."']";

        // Get the names of months (locale-specific)
        $month_names = array();
        for ($i = 1; $i < 13; $i++) {
            $month_names[] = $date->formatLikeIntl('MMM');
            $date->setMonth($i + 1);
        }
        $month_names = "['".implode("', '", $month_names)."']";

        $prev_alt_text     = L::_('Previous Month');
        $next_alt_text     = L::_('Next Month');
        $close_text        = L::_('Close');
        $nodate_text       = L::_('No Date');
        $today_text        = L::_('Today');

        $open_toggle_text  = L::_('open calendar');
        $close_toggle_text = L::_('close calendar');

        return
            "SwatCalendar.week_names = {$week_names};\n".
            "SwatCalendar.month_names = {$month_names};\n".
            "SwatCalendar.prev_alt_text = '{$prev_alt_text}';\n".
            "SwatCalendar.next_alt_text = '{$next_alt_text}';\n".
            "SwatCalendar.close_text = '{$close_text}';\n".
            "SwatCalendar.nodate_text = '{$nodate_text}';\n".
            "SwatCalendar.today_text = '{$today_text}';\n".
            "SwatCalendar.open_toggle_text = '{$open_toggle_text}';\n".
            "SwatCalendar.close_toggle_text = '{$close_toggle_text}';\n";
    }

    // }}}
}
