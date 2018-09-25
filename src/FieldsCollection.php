<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 19.09.2018
 */

namespace Dar\Admin;


use Dar\Admin\Fields\BaseField;
use Illuminate\Support\Collection;

class FieldsCollection extends Collection
{
	/** @var BaseField[] */
	protected $items = [];

	/**
	 * @method get
	 * @param mixed $key
	 * @param null $default
	 *
	 * @return BaseField
	 */
	public function get($key, $default = null)
	{
		if ($this->offsetExists($key)) {
			return $this->items[$key];
		}

		return value($default);
	}
}