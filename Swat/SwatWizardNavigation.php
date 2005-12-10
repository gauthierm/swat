<?php

require_once 'Swat/SwatWizardForm.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatButton.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A wizard navigation class
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWizardNavigation extends SwatControl
{
	private $next_button;
	private $prev_button;

	/**
	 * Creates a new wizard navigation
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->next_button = new SwatButton('nav_next');
		$this->prev_button = new SwatButton('nav_prev');
	}

	/**
	 * Display the navigation
	 *
	 * @throws SwatInvalidClassException
	 */
	public function display()
	{
		if (!$this->parent instanceof SwatWizardForm)
			throw new SwatInvalidClassException(
				'Must be a child of a SwatWizardForm',
				0, $this);

		$div = new SwatHtmlTag('div');
		$div->class = 'swat-wizard-navigation';
		$div->open();

		if ($this->parent->step > 0) {
			$this->prev_button->title = Swat::_('Previous');
			$this->prev_button->display();
		}

		if ($this->parent->step + 1 == $this->parent->getStepCount())
			$this->next_button->title = Swat::_('Submit');
		else
			$this->next_button->title = Swat::_('Next');

		$this->next_button->display();

		$div->close();
	}

	/**
	 * Get next step
	 *
	 * Processes the navigation buttons and chooses which step to go to next
	 *
	 * @return integer Next step
	 */
	public function getNextStep()
	{
		$this->next_button->process();
		$this->prev_button->process();

		$step = $this->parent->step;

		if ($this->next_button->clicked)
			return $step + 1;
		elseif ($this->prev_button->clicked)
			return $step - 1;
		else
			return null;
	}
}

?>
