<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 21.09.18
 */

namespace Dar\Admin\Fields;


class FileField extends BaseField
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

		return parent::render('fields/file.blade.php');

	}


}
