<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin;


use Bitrix\Main\Entity\Base;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Loader;
use Dar\Admin\Exceptions;

class Resource
{

	/** @var null|Resource */
	protected static $instance = null;

	protected $items = [];

	protected $container;

	/** @var array  */
	protected static $resourceCollection = [];

	public static $modules = [];

	public function __construct(AdminContainer $adminContainer = null)
	{
		if(!$adminContainer instanceof AdminContainer){
			$adminContainer = AdminContainer::getInstance();
		}

		$this->container = $adminContainer;
	}

	/**
	 * @method getInstance - get param instance
	 * @return Resource
	 */
	public static function getInstance()
	{
		if(is_null(self::$instance))
			self::$instance = new static();

		return self::$instance;
	}


	public function add($IResource = '', $alias = null, $modules = [])
	{
		if(!$alias){
			$alias = collect(explode('\\', get_class($IResource)))->last();
		}

		static::addResourceStorage($alias, $IResource);

		if(!is_array($modules)){
			$modules = [$modules];
		}
		static::$modules[$alias] = $modules;

		return $this;
	}

	public function get($name)
	{
		return $this->container->make($name);
	}

	/**
	 * @method resolve
	 * @param string $name
	 *
	 * @return mixed
	 * @throws Exceptions\DarAdminException
	 */
	public function resolve($name = '')
	{
		if(!static::$resourceCollection[$name]){
			throw new Exceptions\NotFoundResource('resource '.$name.' not found');
		}

		$class = static::$resourceCollection[$name];

		foreach (static::$modules[$name] as $module){
			if(!Loader::includeModule($module)){
				throw new Exceptions\DarAdminException('Модуль '.$module.' не установлен');
			}
		}

		if(is_subclass_of($class, DataManager::class) || is_subclass_of($class, Base::class)){
			$entityClass = $class;
			unset($class);

			$class = new BasePage();
			$class::$model = $entityClass;
		}

		AdminProvider::register()->setResourceName($name);

		$this->container->bind($name, function ($container) use ($class, $name){
			/** @var BasePage $instance */
			$instance = new $class($container);
//			$name = str_replace('.', '_', $name);

			$instance->setNamePage($name);

			return $instance;
		});

		return $this->get($name);
	}

	/**
	 * @method getResourceCollection - get param resourceCollection
	 * @return array
	 */
	public static function getResourceCollection()
	{
		return self::$resourceCollection;
	}

	/**
	 * @method setResourceCollection - set param ResourceCollection
	 * @param array $resourceCollection
	 */
	public static function setResourceCollection($resourceCollection)
	{
		self::$resourceCollection = $resourceCollection;
	}

	/**
	 * @method findResource
	 * @param $name
	 *
	 * @return null|string
	 */
	public static function findResource($name)
	{
		return static::$resourceCollection[$name] ?: null;
	}

	public static function addResourceStorage($name, $resource)
	{
		static::$resourceCollection[$name] = $resource;
	}
}
