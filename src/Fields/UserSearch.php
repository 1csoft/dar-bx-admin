<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 26.09.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Dar\Admin\BasePage;
use Dar\Admin\Builder\EditBuilder;
use Dar\Admin\Builder\IBuilder;

class UserSearch extends BaseField
{
	/**
	 * UserSearch constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$resource = AdminProvider::instance()->getCurrentResource();
		$params['formName'] = $resource->getNamePage().'_form';

	}


	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
//		$resource = AdminContainer::getInstance()->get(IBuilder::class);
		return parent::render('fields/user.search', $params);
	}

	public function renderList($tpl = false, $params = [])
	{

	}
}