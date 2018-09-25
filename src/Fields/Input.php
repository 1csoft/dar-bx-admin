<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Fields;


class Input extends BaseField
{
	protected $type = 'text';

	public function render($tpl = '')
	{
		return parent::render('fields/input.blade.php');
	}
}