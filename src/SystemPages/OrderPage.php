<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 09.11.2018
 */

namespace Dar\Admin\SystemPages;


use Bitrix\Sale\Internals\OrderTable;
use Dar\Admin\BasePage;
use Dar\Admin\Fields\DateCalendar;
use Dar\Admin\Fields\Input;
use Dar\Admin\Fields\Number;
use Dar\Admin\Fields\Primary;
use Dar\Admin\Fields\UserSlideSearch;
use Dar\Admin\FieldsCollection;

class OrderPage extends BasePage
{
	public static $model = OrderTable::class;

	public function actionRow($data = [])
	{
		$js = \CUtil::PhpToJSObject([
			'elementId' => $data['ID'],
		]);
		return [
			array(
				"ICON" => "edit",
				"DEFAULT" => true,
				"TEXT" => 'Выбрать',
				"ACTION" => "BX.SidePanel.Instance.postMessageTop(window, '".$this->request->query->get('eventName')."', ".$js.");"
			)
		];
	}

	public function fields()
	{
		if(is_null($this->fields)){
			$this->fields = new FieldsCollection();
			$this->fields
				->add(Primary::create('ID')->filterable()->isDefault()->label('ID'))
				->add(Input::create('ACCOUNT_NUMBER')->label('Номер')->filterable())
				->add(UserSlideSearch::create('USER_ID')->label('Покупатель')->isDefault()->filterable())
				->add(Number::create('PAY_SYSTEM_ID')->label('ID платежной системы')->isDefault()->filterable())
				->add(Input::create('STATUS_NAME')->label('Статус')->isDefault()->filterable()->reference('STATUS.NAME'))
				->add(DateCalendar::create('DATE_INSERT')->label('Дата создания')->isDefault()->filterable())
				->add(DateCalendar::create('DATE_UPDATE')->label('Дата изменения')->isDefault()->filterable())
				->add(Number::create('PERSON_TYPE_ID')->label('Тип плательщика')->filterable())
				->add(Input::create('PAYED')->label('Оплачено')->filterable(false))
				->add(Input::create('PRICE')->label('Сумма')->filterable(false)->isDefault());
		}

		return $this->fields;
	}

	public function contextListMenu()
	{
		return [];
	}

	public function groupOperations()
	{
		return [];
	}
}