<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\Model;
use Silverorange\Swat\Util;

/**
 * Visible navigation tool (breadcrumb trail)
 *
 * @package   Swat
 * @copyright 2005-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Model\NavBarEntry
 */
class NavBar extends Control implements \Countable
{
    // {{{ public properties

    /**
     * Whether or not to display the last entry in this navbar as a link
     *
     * If set to false, the last entry is displayed as text even if the last
     * navbar entry has a link. Defaults to true.
     *
     * @var boolean
     */
    public $link_last_entry = true;

    /**
     * Separator characters displayed between each navbar entry in this navbar
     *
     * The default separator is a non-breaking space followed by a right
     * guillemet followed by a breaking space.
     *
     * @var string
     */
    public $separator = ' » ';

    /**
     * Optional container tag for this navigational bar
     *
     * The container tag wraps around all entries in this navigational bar.
     *
     * @var Html\Tag the container tag for this navigational bar.
     */
    public $container_tag;

    // }}}
    // {{{ private properties

    /**
     * Array of Model\NavBarEntry objects displayed in this navbar
     *
     * @var array
     *
     * @see Model\NavBarEntry
     */
    private $entries = array();

    // }}}
    // {{{ public function createEntry()

    /**
     * Creates a Model\NavBarEntry and adds it to the end of this navigation
     * bar
     *
     * @param string $title        the entry title.
     * @param string $link         an optional entry URI.
     * @param string $content_type an optional content type for the entry title.
     */
    public function createEntry($title, $link = null,
        $content_type = 'text/plain')
    {
        $this->addEntry(new Model\NavBarEntry($title, $link, $content_type));
    }

    // }}}
    // {{{ public function addEntry()

    /**
     * Adds a Model\NavBarEntry to the end of this navigation bar
     *
     * @param Model\NavBarEntry $entry the entry to add.
     */
    public function addEntry($entry)
    {
        $this->entries[] = $entry;
    }

    // }}}
    // {{{ public function addEntries()

    /**
     * Adds an array of Model\NavBarEntry to the end of this navigation bar
     *
     * @param array $entries array of entries to add.
     */
    public function addEntries($entries)
    {
        foreach ($entries as $entry)
            $this->entries[] = $entry;
    }

    // }}}
    // {{{ public function addEntryToStart()

    /**
     * Adds a Model\NavBarEntry to the beginning of this navigation bar
     *
     * @param Model\NavBarEntry $entry the entry to add.
     */
    public function addEntryToStart($entry)
    {
        array_unshift($this->entries, $entry);
    }

    // }}}
    // {{{ public function replaceEntryByPosition()

    /**
     * Replaces an entry in this navigation bar
     *
     * If the entry is not in this navigation bar, an exception is thrown.
     *
     * @param integer           $position zero-based ordinal position of the
     *                                    entry to replace.
     * @param Model\NavBarEntry $entry    the navbar entry to replace the
     *                                    entry at the given position with.
     *
     * @return Model\NavBarEntry the replaced entry.
     *
     * @thows Exception\Exception
     */

    public function replaceEntryByPosition($position,
        Model\NavBarEntry $new_entry)
    {
        if (isset($this->entries[$position])) {
            $old_entry = $this->entries[$position];
            $this->entries[$position] = $new_entry;

            return $old_entry;
        }

        throw new Exception\Exception(
            sprintf(
                'Cannot replace element at position %s because NavBar does ' .
                'not contain an entry at position %s.',
                $position,
                $position
            )
        );
    }

    // }}}
    // {{{ public function getEntryByPosition()

    /**
     * Gets an entry from this navigation bar
     *
     * If the entry is not in this navigation bar, an exception is thrown.
     *
     * @param integer $position zero-based ordinal position of the entry to
     *                          fetch.  If position is negative, the entry
     *                          position is counted from the end of the nav
     *                          bar (-1 will return one from the end).  Use
     *                          {@link NavBar::getLastEntry()} to get the last
     *                          entry of this nav bar.
     *
     * @return Model\NavBarEntry the entry.
     *
     * @throws Exception\Exception
     */
    public function getEntryByPosition($position)
    {
        if ($position < 0)
            $position = count($this) + $position - 1;

        if (isset($this->entries[$position])) {
            return $this->entries[$position];
        } else {
            throw new Exception\Exception(
                sprintf(
                    'Navbar does not contain an entry at position %s.',
                    $position
                )
            );
        }
    }

    // }}}
    // {{{ public function getLastEntry()

    /**
     * Gets the last entry from this navigation bar
     *
     * If the navigation bar is empty, an exception is thrown.
     *
     * @return Model\NavBarEntry the entry.
     *
     * @throws Exception\Exception
     */
    public function getLastEntry()
    {
        if (count($this->entries) === 0) {
            throw new Exception\Exception('Navbar is empty.');
        }

        return end($this->entries);
    }

    // }}}
    // {{{ public function count()

    /**
     * Gets the number of entries in this navigational bar
     *
     * This satisfies the Countable interface.
     *
     * @return integer number of entries in this navigational bar.
     */
    public function count()
    {
        return count($this->entries);
    }

    // }}}
    // {{{ public function popEntry()

    /**
     * Pops the last entry off the end of this navigational bar
     *
     * If no entries currently exist, an exception is thrown.
     *
     * @return Model\NavBarEntry the entry that was popped.
     *
     * @throws Exception\Exception
     */
    public function popEntry()
    {
        if (count($this) < 1) {
            throw new Exception\Exception(
                'Cannot pop entry. NavBar does not contain any entries.'
            );
        } else {
            return array_pop($this->entries);
        }
    }

    // }}}
    // {{{ public function popEntries()

    /**
     * Pops one or more entries off the end of this navigational bar
     *
     * If more entries are to be popped than currently exist, an exception is
     * thrown.
     *
     * @param integer $number number of entries to pop off this navigational
     *                        bar.
     *
     * @return array an array of Model\NavBarEntry objects that were popped off
     *               the navagational bar.
     *
     * @throws Exception\Exception
     */
    public function popEntries($number)
    {
        if (count($this) < $number) {
            $count = count($this);
            throw new Exception\Exception(
                sprintf(
                    'Unable to pop %s entries. NavBar only contains %s ' .
                    'entries.',
                    $number,
                    $count
                )
            );
        } else {
            return array_splice($this->entries, -$number);
        }
    }

    // }}}
    // {{{ public function clear()

    /**
     * Clears all entries from this navigational bar
     *
     * @return array an array of Model\NavBarEntry objects that were cleared
     *               from this navagational bar.
     */
    public function clear()
    {
        $entries = $this->entries;
        $this->entries = array();
        return $entries;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this navigational bar
     *
     * Displays each entry separated by a special character and outputs
     * navbar entries with links as anchor tags.
     */
    public function display()
    {
        if (!$this->visible)
            return;

        parent::display();

        $count = count($this);
        $i = 1;

        $container_tag = $this->getContainerTag();
        $container_tag->open();

        foreach ($this->entries as $entry) {
            // display separator
            if ($i > 1) {
                echo Util\String::minimizeEntities($this->separator);
            }

            // link all entries or link all but the last entry
            $link = ($this->link_last_entry || $i < $count);

            $this->displayEntry($entry, $link, ($i == 1));

            $i++;
        }

        $container_tag->close();
    }

    // }}}
    // {{{ protected function displayEntry()

    /**
     * Displays an entry in this navigational bar
     *
     * @param Model\NavBarEntry $entry the entry to display.
     * @param boolean           $link  whether or not to hyperlink the given
     *                                 entry if the entry has a link set.
     * @param boolean           $first whether or not this entry should be
     *                                 displayed as the first entry.
     */
    protected function displayEntry(Model\NavBarEntry $entry,
        $show_link = true, $first = false)
    {
        $title = ($entry->title === null) ? '' : $entry->title;
        $link  = $this->getLink($entry);

        if ($link !== null && $show_link) {
            $a_tag = new Html\Tag('a');
            $a_tag->href = $link;
            if ($first)
                $a_tag->class = 'swat-navbar-first';

            $a_tag->setContent($title, $entry->content_type);
            $a_tag->display();
        } else {
            $span_tag = new Html\Tag('span');
            if ($first)
                $span_tag->class = 'swat-navbar-first';

            $span_tag->setContent($title, $entry->content_type);
            $span_tag->display();
        }
    }

    // }}}
    // {{{ protected function getLink()

    /**
     * Gets the link from an entry.
     *
     * @param Model\NavBarEntry $entry the entry to get the link from.
     *
     * @return string the entries link.
     */
    protected function getLink(Model\NavBarEntry $entry)
    {
        return $entry->link;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this navigational bar
     *
     * @return array the array of CSS classes that are applied to this
     *                navigational bar.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-nav-bar');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
    // {{{ protected function getContainerTag()

    /**
     * Gets the container tag for this navigational bar
     *
     * The container tag wraps around all entries in this navigational bar.
     *
     * @return Html\Tag the container tag for this navigational bar.
     */
    protected function getContainerTag()
    {
        if ($this->container_tag === null) {
            $tag = new Html\Tag('div');
        } else {
            $tag = $this->container_tag;
        }

        $tag->id = $this->id;
        $tag->class = $this->getCSSClassString();
        return $tag;
    }

    // }}}
}
