<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin\Exceptions;


class NotModule extends DarAdminException
{

	public function __construct($msg, $module)
	{
		$message = sprintf($msg, $module);

		parent::__construct($message);
	}
}