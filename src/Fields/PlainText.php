<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 26.09.2018
 */

namespace Dar\Admin\Fields;


class PlainText extends BaseField
{
	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = 'fields/plain.text', $params = [])
	{
		return parent::render($tpl, $params);
	}

}