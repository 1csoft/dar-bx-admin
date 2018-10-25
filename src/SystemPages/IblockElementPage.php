<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 22.10.2018
 */

namespace Dar\Admin\SystemPages;


use Bitrix\Main;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Soft1c\OrmIblock\ElementTable;
use Soft1c\OrmIblock\Property\Property;
use Soft1c\OrmIblock\Property\PropertyData;

class IblockElementPage extends BasePage
{
	protected $iblockId = null;

	/**
	 * IblockElementPage constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->iblockId = $this->request->query->get('iblockId');
	}

	/**
	 * @method getModel
	 * @return \Bitrix\Main\Entity\Base|null
	 */
	public static function getModel()
	{
		if(is_null(static::$entity)){
			$request = AdminContainer::getRequest();
			static::$entity = ElementTable::getEntity($request->query->get('iblockId'));
		}

		return static::$entity;
	}

	public function fields()
	{
		if(is_null($this->fields)){
			$this->fields = parent::fields();
			$this->fields->delete([
				'PREVIEW_TEXT_TYPE', 'DETAIL_TEXT_TYPE', 'SEARCHABLE_CONTENT',
				'SHOW_COUNTER', 'SHOW_COUNTER_START', 'TMP_ID'
			]);

			if(static::$entity->hasField('PROPERTY')){
				$propItems = AdminSupport::convertFields(Property::getEntity($this->iblockId));
				foreach ($propItems as $propItem) {
					if($propItem->getName() !== 'IBLOCK_ELEMENT_ID'){
						$this->fields->add($propItem);
					}
				}
				unset($propItems);
			}

		}

		return $this->fields;
	}

	public function actionRow($data = [])
	{
		$js = \CUtil::PhpToJSObject([
			'elementId' => $data['ID'],
			'iblockId' => $data['IBLOCK_ID'],
			'name' => $data['NAME'],
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

	public function groupOperations()
	{
		return [];
	}


	/**
	 * @method onBeforeExecList
	 * @param Main\Entity\Query $query
	 *
	 * @return Main\Entity\Query
	 */
	public function onBeforeExecList(Main\Entity\Query $query)
	{
		$query->addFilter('IBLOCK_ID', $this->iblockId);

		return $query;
	}


}