<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 10.09.2018
 */

namespace Dar\Admin\Fields;


use Bitrix\Main\Type\DateTime;

class DateCalendar extends BaseField
{
	public function render($tpl = 'fields/calendar.blade.php')
	{
		return parent::render($tpl, ['value' => $this->getValue()->toString()]);
	}

	/**
	 * @method getValue
	 * @return DateTime
	 */
	public function getValue()
	{
		return new DateTime($this->value);
	}

	public function onBeforeRenderField()
	{
		parent::onBeforeRenderField();
	}


}