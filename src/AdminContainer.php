<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin;


use Arrilot\BitrixBlade\BladeProvider;
use Symfony\Component\HttpFoundation\Request;

class AdminContainer extends \Illuminate\Container\Container
{

	/** @var array  */
	protected $options = [];

	public function __construct()
	{
		global $APPLICATION, $USER, $USER_FIELD_MANAGER;

		$this->instance('global.app', $APPLICATION);
		$this->instance('global.user', $USER);
		$this->instance('global.uf', $USER_FIELD_MANAGER);
		$this->instance('admin.resources', Resource::getInstance());

		BladeProvider::register(__DIR__.'/Resources/view');
		$this->instance('admin.view', BladeProvider::getViewFactory());

		self::$instance = $this;
	}



	/**
	 * @method getOptions - get param options
	 * @return array
	 */
	public function getOptions()
	{
		return collect($this->options);
	}

	/**
	 * @param array $options
	 *
	 * @return AdminContainer
	 */
	public function setOptions($options)
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * @method application
	 * @return \CMain
	 */
	public static function application()
	{
		return self::$instance->get('global.app');
	}

	/**
	 * @method userFieldManager
	 * @return \CUserTypeManager
	 */
	public static function userFieldManager()
	{
		return self::$instance->get('global.uf');
	}

	/**
	 * @method getRequest
	 * @return Request
	 */
	public static function getRequest()
	{
		return self::$instance->get('admin.request');
	}
}
