<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin;


use Bitrix\Main\Application;

class Configuration
{
	protected static $fille = null;

	/**
	 * @method getFille - get param fille
	 * @return string
	 */
	public static function getFille()
	{
		return self::$fille;
	}

	/**
	 * @method setFille - set param Fille
	 * @param string $fille
	 */
	public static function setFille($fille)
	{
		self::$fille = $fille;
	}

	public static function readFile()
	{
		if(is_null(static::$fille)){
			$config = \Bitrix\Main\Config\Configuration::getValue('dar.admin')['config'];
			if(strlen($config) == 0){
				$config = '/local/config/dar.admin.php';
			}

			static::setFille($config);
		}

		return require_once(Application::getDocumentRoot().static::getFille());
	}
}