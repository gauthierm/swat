<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDate.php';

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class TableView extends DemoPage
{
	// private function initUI();

	public function initUI()
	{
		$data = array(
			array('images/apple.png', 28, 28, 'Apple', 'red', false, true,
				new SwatDate('2005-09-01'), 0.5),
			array('images/orange.png', 28, 28, 'Orange', 'orange', false, false,
				new SwatDate('2005-04-20'), 0.75),
			array('images/strawberry.png', 28, 28, 'Strawberry', 'red', true, false,
				new SwatDate('2005-07-05'), 0.6)
		);
		
		$table_view = $this->ui->getWidget('table_view');
		$table_store = new SwatTableStore();
		
		foreach ($data as $datum) {
			$fruit = new FruitObject();
			$fruit->image = $datum[0];
			$fruit->image_width = $datum[1];
			$fruit->image_height = $datum[2];
			$fruit->title = $datum[3];
			$fruit->color = $datum[4];
			$fruit->makes_jam = $datum[5];
			$fruit->makes_pie = $datum[6];
			$fruit->harvest_date = $datum[7];
			$fruit->cost = $datum[8];

			$table_store->addRow($fruit);
		}

		$table_view->model = $table_store;

	}

	// }}}
}

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FruitObject
{
	// {{{ public properties

	public $image = '';
	public $image_width = 0;
	public $image_height = 0;
	public $title = '';
	public $color = '';
	public $makes_jam = false;
	public $makes_pie = false;
	public $harvest_date = null;
	public $cost = 0;

	// }}}
}

?>