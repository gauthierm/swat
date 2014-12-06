<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Html;
use Silverorange\Swat\Model;
use Silverorange\Swat\L;

/**
 * Abstract javascript overlay widget.
 *
 * This widget allows creating a java-script run widget that opens an overlay
 * containing the widget's contents. The framework for the overlay is handled
 * by this abstract class (opening, closing, toggle button, etc…), but the
 * functionality of the widget must be handled by sub-classing.
 *
 * Subclasses should, at the very least, sub-class swat-abstract-overlay.js
 * and add the new java-script class using the
 * AbstractOverlay::getInlineJavaScript() method.
 *
 * @package   Swat
 * @copyright 2010-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AbstractOverlay extends InputControl implements Model\State
{
    // {{{ public properties

    /**
     * Access key
     *
     * Access key for this overlay control, for keyboard nagivation.
     *
     * @var string
     */
    public $access_key = null;

    /**
     * Widget value
     *
     * @var string
     */
    public $value = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new overlay widget
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $yui = new Html\YUI(array('dom', 'event', 'container'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addJavaScript(
            'packages/swat/javascript/swat-abstract-overlay.js'
        );

        $this->addJavaScript(
            'packages/swat/javascript/swat-z-index-manager.js'
        );
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this overlay widget
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

        $input_tag = new Html\Tag('input');
        $input_tag->type = 'hidden';
        $input_tag->id = $this->id.'_value';
        $input_tag->name = $this->id;
        $input_tag->value = $this->value;
        $input_tag->accesskey = $this->access_key;

        $input_tag->display();

        $container_div_tag->close();

        Util\JavaScript::displayInline($this->getInlineJavaScript());
    }

    // }}}
    // {{{ public function getState()

    /**
     * Gets the current state of this simple color selector widget
     *
     * @return string the current state of this simple color selector widget.
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
     * Sets the current state of this simple color selector widget
     *
     * @param string $state the new state of this simple color selector widget.
     *
     * @see Model\State::setState()
     */
    public function setState($state)
    {
        $this->value = $state;
    }

    // }}}
    // {{{ abstract protected function getInlineJavaScript()

    /**
     * Gets inline JavaScript
     *
     * @return string overlay inline JavaScript.
     */
    protected function getInlineJavaScript()
    {
        return sprintf(
            "SwatAbstractOverlay.close_text = %s;\n",
            Util\JavaScript::quoteString(L::_('Close'))
        );
    }

    // }}}
}
