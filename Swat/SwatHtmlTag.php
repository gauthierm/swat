<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Stores and outputs an HTML tag.
 */
class SwatHtmlTag extends SwatObject {

	/**
	 * @var string
	 * The name of the HTML tag.
	 */
	public $tagname;

	/**
	 * @var array
	 * Array containing attributes of the HTML tag in the form of
	 * (attr name) => (value).
	 */
	private $attributes;

	/**
	 * @param string $tagname The name of the HTML tag.
	 */
	function __construct($tagname) {
		$this->tagname = $tagname;
		$this->attributes = array();
	}

	function __get($attr) {
		if (isset($this->attributes[$attr]))
			return $this->attributes[$attr];
		else
			throw new SwatException(__CLASS__.": undefined attribute $attr");
	}

	function __set($attr, $val) {
		$this->attributes[$attr] = $val;
	}

	/**
	 * Remove an attribute.
	 * Remove a previously assigned attribute. Useful when one tag object is
	 * displayed multiple times with different attributes.
	 *
	 * @param string $attr The name of attribute to remove.
	 */
	function removeAttr($attr) {
		unset($this->attributes[$attr]);
	}

	/**
	 * Display the tag.
	 * Output the opening tag including all its attributes and implicitly close
	 * the tag.  If explicit closing is desired, use {@link SwatHtmlTag::display()}
	 * instead.
	 */
	public function display() {
		$this->open_internal(true);
	}

	/**
	 * Open the tag.
	 * Output the opening tag including all its attributes. Should be paired
	 * with a call to {@link SwatHtmlTag::close()}.  If implicit closing
	 * is desired, use {@link SwatHtmlTag::display()} instead.
	 */
	public function open() {
		$this->open_internal(false);
	}

	/**
	 * Close the tag.
	 * Output the closing tag. Should be paired with a call to 
	 * {@link SwatHtmlTag::close()}.
	 */
	public function close() {
		echo '</', $this->tagname, '>';
	}

	private function open_internal($implicit_close) {
		echo '<', $this->tagname;

		if ($this->attributes != null) {
			foreach ($this->attributes as $attr => $value) {
				if ($value == null)
					echo ' ', $attr;
				else
					echo ' ', $attr, '="', $value, '"';
			}
		}

		if ($implicit_close)
			echo ' />';
		else
			echo '>';
	}

}

?>
