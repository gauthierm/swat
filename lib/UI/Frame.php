<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;

/**
 * A container with a decorative frame and optional title
 *
 * @package   Swat
 * @copyright 2004-2010 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Frame extends DisplayableContainer implements Titleable
{
    // {{{ public properties

    /**
     * A visible title for this frame, or null
     *
     * @var string
     */
    public $title = null;

    /**
     * An optional visible subtitle for this frame, or null
     *
     * @var string
     */
    public $subtitle = null;

    /**
     * An optional string to separate subtitle from the title
     *
     * @var string
     */
    public $title_separator = ': ';

    /**
     * Optional content type for the title
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $title_content_type = 'text/plain';

    /**
     * Optional header level for the title
     *
     * Setting this will override the automatic heading level calculation
     * based on nesting of frames.
     *
     * @var integer
     */
    public $header_level;

    // }}}
    // {{{ public function getTitle()

    /**
     * Gets the title of this frame
     *
     * Implements the {@link Titleable::getTitle()} interface.
     *
     * @return string the title of this frame.
     */
    public function getTitle()
    {
        if ($this->subtitle === null) {
            return $this->title;
        }

        if ($this->title === null) {
            return $this->subtitle;
        }

        return $this->title . ': ' . $this->subtitle;
    }

    // }}}
    // {{{ public function getTitleContentType()

    /**
     * Gets the title content-type of this frame
     *
     * Implements the {@link Titleable::getTitleContentType()} interface.
     *
     * @return string the title content-type of this frame.
     */
    public function getTitleContentType()
    {
        return $this->title_content_type;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this frame
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        Widget::display();

        $outer_div = new Html\Tag('div');
        $outer_div->id = $this->id;
        $outer_div->class = $this->getCSSClassString();

        $outer_div->open();
        $this->displayTitle();
        $this->displayContent();
        $outer_div->close();
    }

    // }}}
    // {{{ protected function displayTitle()

    /**
     * Displays this frame's title
     */
    protected function displayTitle()
    {
        if ($this->title !== null) {

            $header_tag = new Html\Tag('h' . $this->getHeaderLevel());
            $header_tag->class = 'swat-frame-title';
            $header_tag->setContent($this->title, $this->title_content_type);

            if ($this->subtitle === null) {
                $header_tag->display();
            } else {
                $span_tag = new Html\Tag('span');
                $span_tag->class = 'swat-frame-subtitle';
                $span_tag->setContent(
                    $this->subtitle,
                    $this->title_content_type
                );
                $header_tag->open();
                $header_tag->displayContent();
                echo $this->title_separator;
                $span_tag->display();
                $header_tag->close();
            }
        }
    }

    // }}}
    // {{{ protected function displayContent()

    /**
     * Displays this frame's content
     */
    protected function displayContent()
    {
        $inner_div = new Html\Tag('div');
        $inner_div->class = 'swat-frame-contents';
        $inner_div->open();
        $this->displayChildren();
        $inner_div->close();
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this frame
     *
     * @return array the array of CSS classes that are applied to this frame.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-frame');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
    // {{{ protected function getHeaderLevel()

    protected function getHeaderLevel()
    {
        // default header level is h2
        $level = 2;

        if ($this->header_level === null) {
            $ancestor = $this->parent;

            // get appropriate header level, limit to h6
            while ($ancestor !== null) {
                if ($ancestor instanceof Frame) {
                    $level = $ancestor->getHeaderLevel() + 1;
                    $level = min($level, 6);
                    break;
                }

                $ancestor = $ancestor->parent;
            }
        } else {
            $level = $this->header_level;
        }

        return $level;
    }

    // }}}
}
