<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTextarea.php';
require_once 'Swat/SwatYUI.php';

/**
 * A what-you-see-is-what-you-get (WYSIWYG) XHTML textarea editor widget
 *
 * This textarea editor widget is powered by TinyMCE, which, like Swat is
 * licensed under the LGPL. See {@link http://tinymce.moxiecode.com/} for
 * details.
 *
 * @package   Swat
 * @copyright 2004-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextareaEditor extends SwatTextarea
{
	// {{{ class constants

	const MODE_VISUAL = 1;
	const MODE_SOURCE = 2;

	// }}}
	// {{{ public properties

	/**
	 * Width of the editor
	 *
	 * Specified in CSS units (percent, pixels, ems, etc). If not specified,
	 * the {@link SwatTextarea::$cols} property will determine the width of
	 * the editor.
	 *
	 * @var string
	 */
	public $width = null;

	/**
	 * Height of the editor
	 *
	 * Specified in CSS units (percent, pixels, ems, etc). If not specified,
	 * the {@link SwatTextarea::$rows} property will determine the height of
	 * the editor.
	 *
	 * @var string
	 */
	public $height = null;

	/**
	 * Base-Href
	 *
	 * Optional base-href, used to reference images and other urls in the
	 * editor.
	 *
	 * @var string
	 */
	public $basehref = null;

	/**
	 * Editing mode to use
	 *
	 * Must be one of either {@link SwatTextareaEditor::MODE_VISUAL} or
	 * {@link SwatTextareaEditor::MODE_SOURCE}.
	 *
	 * Defaults to <kbd>SwatTextareaEditor::MODE_VISUAL</kbd>.
	 *
	 * @var integer
	 */
	public $mode = self::MODE_VISUAL;

	/**
	 * Whether or not the mode switching behavior is enabled
	 *
	 * If set to false, only {@link SwatTextAreaEditor::$mode} will be ignored
	 * and only {@link SwatTextAreaEditor::MODE_VISUAL} will be available for
	 * the editor.
	 *
	 * @var boolean
	 */
	public $modes_enabled = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new what-you-see-is-what-you-get XHTML textarea editor
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;
		$this->rows = 30;

		$this->addJavaScript(
			'packages/swat/javascript/tiny_mce/tiny_mce.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		// textarea tags cannot be self-closing when using HTML parser on XHTML
		$value = ($this->value === null) ? '' : $this->value;

		// escape value for display because we actually want to show entities
		// for editing
		$value = htmlspecialchars($value);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-textarea-editor-container';
		$div_tag->open();

		$textarea_tag = new SwatHtmlTag('textarea');
		$textarea_tag->name = $this->id;
		$textarea_tag->id = $this->id;
		$textarea_tag->class = $this->getCSSClassString();
		// NOTE: The attributes rows and cols are required in
		//       a textarea for XHTML strict.
		$textarea_tag->rows = $this->rows;
		$textarea_tag->cols = $this->cols;
		$textarea_tag->setContent($value);
		$textarea_tag->accesskey = $this->access_key;

		// set element styles if width and/or height are specified
		if ($this->width !== null || $this->height !== null) {
			if ($this->width !== null && $this->height !== null) {
				$textarea_tag->style = sprintf('width: %s; height: %s;',
					$this->width, $this->height);
			} elseif ($this->width !== null) {
				$textarea_tag->style = sprintf('width: %s;',
					$this->width);
			} else {
				$textarea_tag->style = sprintf('height: %s;',
					$this->height);
			}
		}

		if (!$this->isSensitive())
			$textarea_tag->disabled = 'disabled';

		$textarea_tag->display();

		// hidden field to preserve editing mode in form data
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';
		$input_tag->id = $this->id.'_mode';
		$input_tag->value = $this->mode;
		$input_tag->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

	/**
	 * Gets the id attribute of the XHTML element displayed by this widget
	 * that should receive focus
	 *
	 * @return string the id attribute of the XHTML element displayed by this
	 *                 widget that should receive focus or null if there is
	 *                 no such element.
	 *
	 * @see SwatWidget::getFocusableHtmlId()
	 */
	public function getFocusableHtmlId()
	{
		return null;
	}

	// }}}
	// {{{ protected function getConfig()

	protected function getConfig()
	{
		$buttons = implode(',', $this->getConfigButtons());

		$formats = array(
			'p',
			'blockquote',
			'pre',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
		);

		$formats = implode(',', $formats);

		$modes = ($this->modes_enabled) ? 'yes' : 'no';

		$config = array(
			'mode'                              => 'exact',
			'elements'                          => $this->id,
			'theme'                             => 'advanced',
			'theme_advanced_buttons1'           => $buttons,
			'theme_advanced_buttons2'           => '',
			'theme_advanced_buttons3'           => '',
			'theme_advanced_toolbar_location'   => 'top',
			'theme_advanced_toolbar_align'      => 'left',
			'theme_advanced_blockformats'       => $formats,
			'skin'                              => 'swat',
			'plugins'                           => 'swat,media',
			'swat_modes_enabled'                => $modes,
		);

		return $config;
	}

	// }}}
	// {{{ protected function getConfigButtons()

	protected function getConfigButtons()
	{
		return array(
			'bold',
			'italic',
			'|',
			'formatselect',
			'|',
			'removeformat',
			'|',
			'undo',
			'redo',
			'|',
			'outdent',
			'indent',
			'|',
			'bullist',
			'numlist',
			'|',
			'link',
			'image',
			'snippet',
		);
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	protected function getInlineJavaScript()
	{
		$base_href = 'editor_base_'.$this->id;
		ob_start();

		echo "var {$base_href} = ".
			"document.getElementsByTagName('base');\n";

		echo "if ({$base_href}.length) {\n";
		echo "\t{$base_href} = {$base_href}[0];\n";
		echo "\t{$base_href} = {$base_href}.href;\n";
		echo "} else {\n";
		echo "\t{$base_href} = location.href.split('#', 2)[0];\n";
		echo "\t{$base_href} = {$base_href}.split('?', 2)[0];\n";
		echo "\t{$base_href} = {$base_href}.substring(\n";
		echo "\t\t0, {$base_href}.lastIndexOf('/') + 1);\n";
		echo "}\n";

		echo "tinyMCE.init({\n";

		$lines = array();
		foreach ($this->getConfig() as $name => $value) {
			if (is_string($value)) {
				$value = SwatString::quoteJavaScriptString($value);
			} elseif (is_bool($value)) {
				$value = ($value) ? 'true' : 'false';
			}
			$lines[] = "\t".$name.": ".$value;
		}

		$lines[] = "\tdocument_base_url: editor_base_{$this->id}";

		echo implode(",\n", $lines);
		echo "\n});";

		return ob_get_clean();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this textarea
	 *
	 * @return array the array of CSS classes that are applied to this textarea.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-textarea-editor');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
