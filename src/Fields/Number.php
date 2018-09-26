<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 06.09.2018
 */

namespace Dar\Admin\Fields;


class Number extends BaseField
{
	public function render($tpl = '')
	{
		return parent::render('fields/number');
	}

}