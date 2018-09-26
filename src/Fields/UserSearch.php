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
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
//		$resource = AdminContainer::getInstance()->get(IBuilder::class);
		$resource = AdminProvider::instance()->getCurrentResource();
		$params['formName'] = $resource->getNamePage().'_form';

		return parent::render('fields/user.search', $params);
	}

}