<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 29.10.2018
 */

namespace Dar\Admin\Fields;


class HideInput extends BaseField
{
	public function render($tpl = 'fields/hidden', $params = [])
	{
		return parent::render($tpl, $params);
	}

}