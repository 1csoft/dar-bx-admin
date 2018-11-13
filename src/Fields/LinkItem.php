<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.11.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\AdminContainer;
use Dar\Admin\Uri;

class LinkItem extends Input
{
	public function render($tpl = 'fields/inputLink', $params = [])
	{
		return parent::render($tpl, $params);
	}


	public function renderList($tpl = false, $params = [])
	{
		$tpl = 'fields/inputLink';

		$request = AdminContainer::getRequest();
		$uri = new Uri($request->getUri());
		$uri->setParam('_type', 'EDIT');
		$params['link'] = $uri->getUri();

		return parent::renderList($tpl, $params);
	}

	public function getValue()
	{
		$val = parent::getValue();
		return $val;
	}

}