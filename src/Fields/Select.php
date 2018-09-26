<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\AdminContainer;

class Select extends BaseField
{
	protected $type = 'select';

	protected $items = [];

	protected $multi = false;

	public function options($items = [])
	{
		foreach ($items as $k => $item) {
			if(is_array($item)){
				$this->items[$k] = $item;
			} else {
				$this->items[$k] = [
					'value' => $item,
					'label' => $item
				];
			}
		}

		return $this;
	}

	public function multibale($flag = true)
	{
		$this->multi = $flag;
	}

	public function render($tpl = 'fields/select')
	{
		return parent::render($tpl, ['name' => $this->getName(), 'items' => $this->getItems()]);
	}

	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @method create
	 * @param $name
	 *
	 * @return Select
	 */
	public static function create($name): Select
	{
		return parent::create($name);
	}


}