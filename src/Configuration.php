<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin;


use Bitrix\Main\Application;
use Dar\Admin\SystemPages;

class Configuration
{
	protected static $file = null;

	/**
	 * @method getFile - get param file
	 * @return array
	 */
	public static function getFile(): array
	{
		if(file_exists(self::$file)){
			return require_once(self::$file);
		}
		return [];
	}

	/**
	 * @method setFile - set param File
	 * @param string $fille
	 */
	public static function setFile($file)
	{
		self::$file = $file;
	}

	public static function readFile()
	{
		if(is_null(static::$file)){
			$config = \Bitrix\Main\Config\Configuration::getValue('dar.admin')['config'];
			if(strlen($config) == 0){
				$config = '/local/config/dar.admin.php';
			}

			static::setFile(Application::getDocumentRoot().$config);
		}

		$configData = static::getFile();
		$configData['entities']['iblock.elements'] = SystemPages\IblockElementPage::class;
		$configData['entities']['user.search'] = SystemPages\UserSearchPage::class;


		return $configData;
	}
}