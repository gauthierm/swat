<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat\UI;

use Silverorange\Swat\Exception;
use Silverorange\Swat\Html;

/**
 * An abstract class from which to derive recordset views
 *
 * @package   Swat
 * @copyright 2004-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class View extends Control
{
    // {{{ public properties

    /**
     * A data structure that holds the data to display in this view
     *
     * The data structure used is some form of {@link Model\TableModel}.
     *
     * @var Model\TableModel
     */
    public $model = null;

    // }}}
    // {{{ protected properties

    /**
     * The selections of this view
     *
     * This is an array of {@link ViewSelection} objects indexed by
     * selector id.
     *
     * @var array
     */
    protected $selections = array();

    /**
     * The selectors of this view
     *
     * This is an array of {@link ViewSelector} objects indexed by selector
     * id.
     *
     * @var array
     */
    protected $selectors = array();

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new recordset view
     *
     * @param string $id a non-visible unique id for this recordset view.
     *
     * @see Widget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $yui = new Html\YUI(array('dom'));
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript('packages/swat/javascript/swat-view.js');
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this view
     */
    public function init()
    {
        parent::init();

        // add selectors of this view but not selectors of sub-views
        $selector = '\Silverorange\Swat\UI\ViewSelector';
        $selectors = $this->getDescendants($selector);

        $view = '\Silverorange\Swat\UI\View';
        foreach ($selectors as $selector) {
            if ($selector->getFirstAncestor($view) === $this) {
                $this->addSelector($selector);
            }
        }
    }

    // }}}
    // {{{ public function getSelection()

    /**
     * Gets a selection of this view
     *
     * Selections are an iterable, countable set of row identifiers for rows
     * processed in this view that were selected (in some way) by the user.
     *
     * @param ViewSelector|string $selector optional. The view selector
     *                                      object or the view selector
     *                                      identifier for which to get
     *                                      the selection. Use this
     *                                      parameter if this view has
     *                                      multiple selectors. By default,
     *                                      the first selector in the view
     *                                      is used.
     *
     * @return ViewSelection the selection of this view for the specified
     *                        selector.
     *
     * @throws Exception\ObjectNotFoundException if the <i>$selector</i>
     *         parameter is specified as a string and this view does not
     *         contain a selector with the given identifier.
     * @throws Exception\InvalidClassException if the <i>$selector</i>
     *         parameter is specified as an object that is not a
     *         {@link ViewSelector}.
     * @throws Exception\Exception if the <i>$selector</i> parameter is
     *         specified as a <i>ViewSelector</i> but the selector does not
     *         belong to this view.
     * @throws Exception\Exception if the <i>$selector</i> parameter is
     *         specified and this view has no selectors.
     */
    public function getSelection($selector = null)
    {
        if ($selector === null) {
            if (count($this->selectors) > 0) {
                $selector = reset($this->selectors);
            } else {
                throw new Exception\Exception(
                    'This view does not have any selectors.'
                );
            }
        } elseif (is_string($selector)) {
            if (isset($this->selectors[$selector])) {
                $selector = $this->selectors[$selector];
            } else {
                throw new Exception\ObjectNotFoundException(
                    "Selector with an id of {$selector} does not exist in " .
                    "this view.",
                    0,
                    $selector
                );
            }
        } elseif (!$selector instanceof ViewSelector) {
            throw new Exception\InvalidClassException(
                'Specified object is not a ViewSelector object.',
                0,
                $selector
            );
        } elseif (!isset($this->selections[$selector->getId()])) {
            throw new Exception\Exception(
                'Specified ViewSelector is not a selector of this view.'
            );
        }

        return $this->selections[$selector->getId()];
    }

    // }}}
    // {{{ public function setSelection()

    /**
     * Sets a selection of this view
     *
     * Use by {@link ViewSelector} objects during the processing phase to
     * set the selection of this view for a particular selector.
     *
     * This method may also be used to override the selection provided by a
     * selector.
     *
     * @param ViewSelection       $selection the selection object to set.
     * @param ViewSelector|string $selector  optional. The view selector
     *                                       object or the view selector
     *                                       identifier for which to get
     *                                       the selection. Use this
     *                                       parameter if this view has
     *                                       multiple selectors. By default,
     *                                       the first selector in the view
     *                                       is used.
     *
     * @throws Exception\ObjectNotFoundException if the <i>$selector</i>
     *         parameter is specified as a string and this view does not
     *         contain a selector with the given identifier.
     * @throws Exception\InvalidClassException if the <i>$selector</i>
     *         parameter is specified as an object that is not a
     *         {@link ViewSelector}.
     * @throws Exception\Exception if the <i>$selector</i> parameter is
     *         specified as a <i>ViewSelector</i> but the selector does not
     *         belong to this view.
     * @throws Exception\Exception if the <i>$selector</i> parameter is
     *         specified and this view has no selectors.
     */
    public function setSelection(ViewSelection $selection, $selector = null)
    {
        if ($selector === null) {
            if (count($this->selectors) > 0) {
                $selector = reset($this->selectors);
            } else {
                throw new Exception\Exception(
                    'This view does not have any selectors.'
                );
            }
        } elseif (is_string($selector)) {
            if (isset($this->selectors[$selector])) {
                $selector = $this->selectors[$selector];
            } else {
                throw new Exception\ObjectNotFoundException(
                    "Selector with an id of {$selector} does not exist in " .
                    "this view.",
                    0,
                    $selector
                );
            }
        } elseif (!$selector instanceof ViewSelector) {
            throw new Exception\InvalidClassException(
                'Specified object is not a ViewSelector object.',
                0,
                $selector
            );
        } elseif (!isset($this->selections[$selector->getId()])) {
            throw new Exception\Exception(
                'Specified ViewSelector is not a selector of this view.'
            );
        }

        $this->selections[$selector->getId()] = $selection;
    }

    // }}}
    // {{{ protected final function addSelector()

    /**
     * This method should be called internally by the
     * {@link View::init() method on all descendant UI-objects that are
     * ViewSelector objects.
     */
    protected final function addSelector(ViewSelector $selector)
    {
        $this->selections[$selector->getId()] = new ViewSelection(array());
        $this->selectors[$selector->getId()] = $selector;
    }

    // }}}
}
