<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin\Exceptions;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundResource extends NotFoundHttpException
{
	public function __construct($message = null, \Exception $previous = null, $code = 0, array $headers = array())
	{
		parent::__construct($message, $previous, $code, $headers);
	}


}
