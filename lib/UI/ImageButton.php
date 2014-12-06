<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;
use Silverorange\Swat\Util;

/**
 * An image button widget
 *
 * This widget displays as an XHTML form image button, so it must be used
 * within {@link Form}.
 *
 * @package   Swat
 * @copyright 2008-2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ImageButton extends Button
{
    // {{{ public properties

    /**
     * Image
     *
     * The src attribute in the XHTML input tag.
     *
     * @var string
     */
    public $image;

    /**
     * Optional array of values to substitute into the image property
     *
     * Uses vsprintf() syntax, for example:
     *
     * <code>
     * $my_image->image = 'mydir/%s.%s';
     * $my_image->values = array('myfilename', 'ext');
     * </code>
     *
     * @var array
     */
    public $values = array();

    /**
     * Image alt text
     *
     * The alt attribute in the input tag.
     *
     * @var string
     */
    public $alt = null;

    // }}}
    // {{{ public function process()

    /**
     * Does button processing
     *
     * Sets whether this button has been clicked and also updates the form
     * this button belongs to with a reference to this button if this button
     * submitted the form.
     */
    public function process()
    {
        Widget::process();

        $data = &$this->getForm()->getFormData();

        // images submit id_x, and id_y post vars
        if (isset($data[$this->id.'_x'])) {
            $this->clicked = true;
            $this->getForm()->button = $this;
        }
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this image button
     *
     * Outputs an XHTML input tag.
     *
     * @throws Exception\Exception if the alt property is not set.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        Widget::display();

        if ($this->alt == '') {
            throw new Exception\Exception(
                'The $alt property of ImageButton must be set to an ' .
                'appropriate value. The "alt" attribute is required in ' .
                'HTML5 and can not be an empty string.'
            );
        }

        $input_tag = new Html\Tag('input');
        $input_tag->type = 'image';
        $input_tag->id = $this->id;
        $input_tag->name = $this->id;
        $input_tag->value = $this->title;
        $input_tag->alt = $this->alt;
        $input_tag->class = $this->getCSSClassString();

        if (count($this->values)) {
            $input_tag->src = vsprintf($this->image, $this->values);
        } else {
            $input_tag->src = $this->image;
        }

        $input_tag->tabindex = $this->tab_index;
        $input_tag->accesskey = $this->access_key;

        if (!$this->isSensitive()) {
            $input_tag->disabled = 'disabled';
        }

        $input_tag->display();

        if ($this->show_processing_throbber ||
            $this->confirmation_message !== null) {
            Util\JavaScript::displayInline($this->getInlineJavaScript());
        }
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this button
     *
     * @return array the array of CSS classes that are applied to this button.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-image-button');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
