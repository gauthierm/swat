<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';

/**
 * A control to display page status messages  
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageBox extends SwatControl
{
	/**
	 * The messages to display in this box
	 *
	 * The messages are stored as an array of references to SwatMessage
	 * objects.
	 *
	 * @var array
	 *
	 * @see SwatMessage
	 */
	public $messages = array();

	/**
	 * Displays this message box
	 *
	 * The CSS class of the box is determined by the type of message being
	 * displayed.
	 */
	public function display()
	{
		if (count($this->messages) == 0)
			return;

		$div = new SwatHtmlTag('div');
		$div->class = 'swat-message-box';

		$div->open();
		
		$message_div = new SwatHtmlTag('div');

		foreach ($this->messages as $message) {
			switch ($message->type) {
				case SwatMessage::NOTIFICATION :
					$message_div->class = 'swat-message-box-notification';
					break;
				case SwatMessage::WARNING :
					$message_div->class = 'swat-message-box-warning';
					break;
				case SwatMessage::ERROR :
					$message_div->class = 'swat-message-box-error';
					break;
				case SwatMessage::SYSTEM_ERROR :
					$message_div->class = 'swat-message-box-system-error';
					break;
			}

			$message_div->content = $message->primary_content;
			// TODO: do something better with the secondary content
			$message_div->content .= $message->secondary_content;
			$message_div->display();
		}

		$div->close();
	}
}

?>
