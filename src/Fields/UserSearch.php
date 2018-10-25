<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 26.09.2018
 */

namespace Dar\Admin\Fields;


use Bitrix\Main\UserTable;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Dar\Admin\Builder\EditBuilder;
use Dar\Admin\Builder\IBuilder;

class UserSearch extends BaseField
{
	protected $options;

	protected $user = null;

	public function __construct($name = '')
	{
		parent::__construct($name);
		$resource = AdminProvider::instance()->getCurrentResource();
		$this->options['formName'] = $resource->getNamePage().'_form';
	}


	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = 'fields/user.search', $params = [])
	{
//		$resource = AdminContainer::getInstance()->get(IBuilder::class);
		$arUser = static::getUserItem($this->value);
		$this->options['userName'] = $arUser['LOGIN'];

		return parent::render($tpl, $params);
	}

	public function renderList($tpl = false, $params = [])
	{

	}

	/**
	 * @method getUserItem
	 * @param null $id
	 *
	 * @return array|null
	 */
	public static function getUserItem($id = null)
	{
		$id = (int)$id;
		if($id == 0){
			$id = AdminSupport::getUser()->GetID();
		}
		return UserTable::getRow([
			'select' => ['ID', 'NAME', 'EMAIL', 'LOGIN'],
			'filter' => ['=ID' => $id]
		]);
	}

	/**
	 * @method getUserData
	 * @param null $id
	 *
	 * @return array|null
	 */
	public function getUserData($id = null)
	{
		if(is_null($this->user))
			$this->user = static::getUserItem($id);

		return $this->user;
	}
}