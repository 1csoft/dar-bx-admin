<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 23.10.2018
 */

namespace Dar\Admin\SystemPages;


use Dar\Admin\BasePage;
use Bitrix\Main;

class UserSearchPage extends BasePage
{
	public static $model = Main\UserTable::class;

	public function getTitle(): string
	{
		return 'Поиск пользователя';
	}

	public function groupOperations()
	{
		return [];
	}

	public function actionRow($data = [])
	{
		$js = \CUtil::PhpToJSObject([
			'elementId' => $data['ID'],
			'login' => $data['LOGIN'],
			'email' => $data['EMAIL'],
			'name' => $data['EMAIL']
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
			$this->fields = parent::fields();
			$this->fields->delete([
				'PASSWORD', 'BX_USER_ID'
			]);
			$this->fields->get('ID')->isDefault();
			$this->fields->get('LOGIN')->isDefault();
			$this->fields->get('EMAIL')->isDefault();
			$this->fields->get('ID')->filterable();
		}

		return $this->fields;
	}

	public function contextListMenu()
	{
		return [];
	}
}