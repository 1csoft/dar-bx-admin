<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin;


use Bitrix\Main;

interface IResource
{
	/**
	 * @method getTitle
	 * @return string
	 */
	public function getTitle(): string;

	public function getTabs();

//	public function content();

	/**
	 * @method getModel
	 * @return Main\Entity\Base|null
	 */
	public static function getModel();

	public function getType(): string;

	/**
	 * @method getUrl
	 * @param string $name
	 * @return array|string
	 */
	public function getUrl($name = '');

	/**
	 * @method setUrl - set param Url
	 * @param array $url
	 */
	public function setUrl($url);

	/**
	 * @method getEntity
	 * @return Main\Entity\Base
	 */
	public function getEntity(): Main\Entity\Base;
}
