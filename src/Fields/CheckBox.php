<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 24.10.2018
 */

namespace Dar\Admin\Fields;


class CheckBox extends BaseField
{

	protected $type = 'bool';

	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = 'fields/checkbox', $params = [])
	{
		return parent::render($tpl, $params);
	}

	/**
	 * @method getType - get param type
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return CheckBox
	 */
	public function type(string $type): CheckBox
	{
		$this->type = $type;

		return $this;
	}

	public function items(array $items = [])
	{

		$this->options['items'] = $items;

		return $this;
	}
}