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

	public function render($tpl = '', $params = [])
	{
		$tpl = strlen($tpl) > 0 ? $tpl : 'fields/input';
		$params['type'] = $params['type'] ?: 'text';

		return parent::render($tpl, $params);
	}


}