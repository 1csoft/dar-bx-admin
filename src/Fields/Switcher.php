<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 25.10.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\AdminContainer;

class Switcher extends BaseField
{
	protected $type = 'int';

	protected $request;

	public function __construct($name = null)
	{
		parent::__construct($name);

		$this->request = AdminContainer::getRequest();
	}


	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = 'fields/switcher', $params = [])
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
	 * @return Switcher
	 */
	public function type(string $type): Switcher
	{
		$this->type = $type;


		return $this;
	}

	/**
	 * @param mixed $value
	 *
	 * @return BaseField
	 */
	public function value($value)
	{

		if ($this->value !== $value && AdminContainer::getRequest()->request->has($this->name)){
			// todo переписать сей костылёк
			if($value == 1)
				$value = 'on';

			switch ($this->type) {
				case 'int':
				case 'integer':
					$this->value = $value == 'on' ? 1 : 0;
					break;
				case 'bool':
				case 'boolean':
					$this->value = $value == 'on' ? true : false;
					break;
				case 'str':
				case 'string':
					$this->value = $value == 'on' ? 'Y' : 'N';
					break;
			}
		} else {
			$this->value = $value;
		}

		return $this;
	}

	/**
	 * @method getValue - get param value
	 * @return mixed
	 */
	public function getValue()
	{
		switch ($this->type) {
			case 'bool':
			case 'boolean':
				return $this->value == true ? 1 : 0;
				break;
			case 'str':
			case 'string':
				return $this->value == 'Y' ? 1 : 0;
				break;
			default:
				return $this->value;
		}
	}

}