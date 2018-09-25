<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 18.09.18
 */

namespace Dar\Admin\Exceptions;


use Throwable;
use Exception;

class DarAdminException extends Exception
{
	/**
	 * DarAdminException constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct($message = "", int $code = 0, Throwable $previous = null)
	{
		if(is_array($message)){
			$message = implode(', ', $message);
		}

		$message = lcfirst($message);
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @method __toString
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('Административная ошибка: [%d] %s', $this->getCode(), $this->getMessage());
	}


}
