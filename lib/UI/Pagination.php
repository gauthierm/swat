<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\L;

/**
 * A widget to allow navigation between paged data
 *
 * Pagination pages start at page 1, not page 0.
 *
 * @package   Swat
 * @copyright 2004-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Pagination extends Control
{
    // {{{ class constants

    /**
     * Display part constant for the displaying 'next' link
     */
    const NEXT     = 1;

    /**
     * Display part constant for displaying the 'previous' link
     */
    const PREV     = 2;

    /**
     * Display part constant for displaying a textual description of the
     * current position
     */
    const POSITION = 4;

    /**
     * Display part constant for displaying a list of pages close to the
     * current page
     */
    const PAGES    = 8;

    // }}}
    // {{{ public properties

    /**
     * The URI linked by this pagination widget
     *
     * The first sprintf placeholder (%s) in the URI will be replaced with
     * the current page number. For example: "mydir/page%s".
     *
     * @var string
     */
    public $link = null;

    /**
     * The number of records displayed on a page
     *
     * @var integer
     */
    public $page_size = 20;

    /**
     * The total number of records that are available for display
     *
     * @var integer
     */
    public $total_records = 0;

    /**
     * The index of the first record being displayed on the current page
     *
     * @var integer
     */
    public $current_record = 0;

    /**
     * The text label displayed in the Next link
     *
     * Defaults to "Next »".
     *
     * @var string
     */
    public $next_label;

    /**
     * The text label displayed in the Previous link
     *
     * Defaults to "« Previous".
     *
     * @var string
     */
    public $previous_label;

    /**
     * Displayed pagination parts
     *
     * The display parts property is a bitwise combination of the
     * {@link Pagination::PREV}, {@link Pagination::NEXT},
     * {@link Pagination::PAGES} and {@link Pagination::POSITION}
     * constants.
     *
     * For example, to show a pagination widget with just next and previous
     * links use the following:
     *
     * <code>
     * $pagination->display_parts = Pagination::PREV |
     *     Pagination::NEXT;
     * </code>
     *
     * Defaults to <code>Pagination::POSITION | Pagination::NEXT |
     * Pagination::PREV | Pagination::PAGES</code>.
     *
     * @var integer
     */
    public $display_parts;

    // }}}
    // {{{ protected properties

    /**
     * The next page to display
     *
     * @var integer
     */
    protected $next_page = 0;

    /**
     * The previous page to display
     *
     * @var integer
     */
    protected $prev_page = 0;

    /**
     * The current page number
     *
     * @var integer
     */
    protected $current_page = 1;

    /**
     * The total number of pages in the record set
     *
     * This is not the number of <i>records</i> in the record set.
     *
     * @var integer
     */
    protected $total_pages = 0;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new pagination widget
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $this->display_parts  = self::POSITION | self::NEXT |
                                self::PREV | self::PAGES;

        // These strings include a non-breaking space
        $this->previous_label = L::_('‹ Previous');
        $this->next_label = L::_('Next ›');

        $this->addStyleSheet('packages/swat/styles/swat-pagination.css');
    }

    // }}}
    // {{{ public function getResultsMessage()

    /**
     * Gets a human readable summary of the current state of this pagination
     * widget
     *
     * @param string $unit        optional. The type of unit being returned. By
     *                            default this is 'record'.
     * @param string $unit_plural optional. The plural version of the
     *                            <i>$unit</i> parameter. By default this is
     *                            'records'.
     *
     * @return string a human readable summary of the current state of this
     *                pagination widget.
     */
    public function getResultsMessage($unit = null, $unit_plural = null)
    {
        if ($unit === null) {
            $unit = L::_('record');
        }

        if ($unit_plural === null) {
            $unit_plural = L::_('records');
        }

        $message = '';

        if ($this->total_records == 0) {
            $message = sprintf(L::_('No %s.'), $unit_plural);
        } elseif ($this->total_records == 1) {
            $message = sprintf(L::_('One %s.'), $unit);
        } else {
            $locale = I18N\Locale::get();
            $message = sprintf(
                L::_('%s %s, displaying %s to %s'),
                $locale->formatNumber($this->total_records),
                $unit_plural,
                $locale->formatNumber($this->current_record + 1),
                $locale->formatNumber(
                    min(
                        $this->current_record + $this->page_size,
                        $this->total_records
                    )
                )
            );
        }

        return $message;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this pagination widget
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->calculatePages();

        if ($this->total_pages > 1) {
            $div_tag = new Html\Tag('div');
            $div_tag->id = $this->id;
            $div_tag->class = $this->getCSSClassString();
            $div_tag->open();

            if ($this->display_parts & self::POSITION) {
                $this->displayPosition();
            }
            if ($this->display_parts & self::PREV) {
                $this->displayPrev();
            }
            if ($this->display_parts & self::PAGES) {
                $this->displayPages();
            }
            if ($this->display_parts & self::NEXT) {
                $this->displayNext();
            }
            $div_tag->close();
        }
    }

    // }}}
    // {{{ public function setCurrentPage()

    /**
     * Set the current page that is displayed
     *
     * Calculates the current_record properties.
     *
     * @param integer $page The current page being displayed.
     */
    public function setCurrentPage($page)
    {
        $this->current_page = max(1, (int)$page);
        $this->current_record = ($this->current_page - 1) * $this->page_size;
    }

    // }}}
    // {{{ public function getCurrentPage()

    /**
     * Get the current page that is displayed
     *
     * @return integer The current page being displayed.
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    // }}}
    // {{{ protected function displayPrev()

    /**
     * Displays the previous page link
     */
    protected function displayPrev()
    {
        if ($this->prev_page > 0) {
            $link = $this->getLink();

            $anchor = new Html\Tag('a');
            $anchor->href = sprintf($link, (string)$this->prev_page);
            // this is a non-breaking space
            $anchor->setContent($this->previous_label);
            $anchor->class = 'swat-pagination-nextprev';
            $anchor->display();
        } else {
            $span = new Html\Tag('span');
            $span->class = 'swat-pagination-nextprev';
            $span->setContent($this->previous_label);
            $span->display();
        }
    }

    // }}}
    // {{{ protected function displayPosition()

    /**
     * Displays the current page position
     *
     * i.e. "1 of 3"
     */
    protected function displayPosition()
    {
        $div = new Html\Tag('div');
        $div->class = 'swat-pagination-position';

        $div->setContent(
            sprintf(
                L::_('Page %d of %d'),
                $this->current_page,
                $this->total_pages
            )
        );

        $div->display();
    }

    // }}}
    // {{{ protected function displayNext()

    /**
     * Displays the next page link
     */
    protected function displayNext()
    {
        if ($this->next_page > 0) {
            $link = $this->getLink();

            $anchor = new Html\Tag('a');
            $anchor->href = sprintf($link, (string)$this->next_page);
            // this is a non-breaking space
            $anchor->setContent($this->next_label);
            $anchor->class = 'swat-pagination-nextprev';
            $anchor->display();
        } else {
            $span = new Html\Tag('span');
            $span->class = 'swat-pagination-nextprev';
            // this is a non-breaking space
            $span->setContent($this->next_label);
            $span->display();
        }
    }

    // }}}
    // {{{ protected function displayPages()

    /**
     * Displays a smart list of pages
     */
    protected function displayPages()
    {
        $j = 0;

        $link = $this->getLink();

        $anchor = new Html\Tag('a');
        $span = new Html\Tag('span');
        $current = new Html\Tag('span');
        $current->class = 'swat-pagination-current';

        for ($i = 1; $i <= $this->total_pages; $i++) {
            $display = false;

            if ($this->current_page < 7 && $i <= 10) {
                // Current page is in the first 6, show the first 10 pages
                $display = true;
            } elseif ($this->current_page > $this->total_pages - 6 &&
                $i >= $this->total_pages - 10) {

                // Current page is in the last 6, show the last 10 pages
                $display = true;
            } elseif ($i < 3 || $i > $this->total_pages - 2 ||
                abs($this->current_page - $i) <= 3) {

                // Always show the first 2, last 2, and middle 6 pages
                $display = true;
            }

            if ($display) {
                if ($j + 1 != $i) {
                    // ellipses
                    $span->setContent('…');
                    $span->display();
                }

                if ($i == $this->current_page) {
                    $current->setContent((string)$i);
                    $current->display();
                } else {
                    $anchor->href = sprintf($link, (string)$i);
                    $anchor->title = sprintf(L::_('Go to page %d'), $i);
                    $anchor->setContent((string)($i));
                    $anchor->display();
                }

                $j = $i;
            }
        }
    }

    // }}}
    // {{{ protected function getLink()

    /**
     * Gets the base link for all page links
     *
     * @return string the base link for all pages.
     */
    protected function getLink()
    {
        return ($this->link === null) ? '%s' : $this->link;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this pagination
     * widget
     *
     * @return array the array of CSS classes that are applied to this
     *               pagination widget.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-pagination');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
    // {{{ protected function calculatePages()

    /**
     * Calculates page totals
     *
     * Sets the internal total_pages, next_page and prev_page properties.
     */
    protected function calculatePages()
    {
        $this->total_pages = ceil($this->total_records / $this->page_size);

        if (($this->total_pages <= 1) ||
            ($this->total_pages == $this->current_page)) {
            $this->next_page = 0;
        } else {
            $this->next_page = $this->current_page + 1;
        }

        if ($this->current_page > 0) {
            $this->prev_page = $this->current_page - 1;
        } else {
            $this->prev_page = 0;
        }
    }

    // }}}
}
