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

	/**
	 * @method add
	 * @param BaseField $field
	 *
	 * @return $this
	 */
	public function add(BaseField $field)
	{
		if($this->items[$field->getName()]){
			$label = $this->get($field->getName())->getLabel();
			$field->label($label);
		}

		$this->delete($field->getName());
		$this->offsetSet($field->getName(), $field);

//		$this->items[$field->getName()] = $field;

		return $this;
	}

	/**
	 * @method delete
	 * @param array|string $item
	 */
	public function delete($item)
	{
		if(is_array($item)){
			foreach ($item as $value) {
				$this->offsetUnset($value);
			}
		} else {
			$this->offsetUnset($item);
		}
	}
}
