<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Fields;


class Textarea extends BaseField
{
	protected $options = [
		'cols' => 80,
		'rows' => 12
	];

	public function render()
	{
		return parent::render('fields/textarea.blade.php');
	}

}