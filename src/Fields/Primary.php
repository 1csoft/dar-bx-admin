<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Fields;

use Dar\Admin\AdminContainer;

class Primary extends BaseField
{
	public function render($tpl = '', $params = [])
	{
		$val = AdminContainer::getRequest()->get($this->getName());

		if(!empty($val))
			$this->value($val);

		return parent::render('fields/primary');
	}


}