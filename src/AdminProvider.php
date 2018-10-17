<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin;

use Bitrix\Main\Loader;
use Dar\Admin\Builder\IBuilder;
use Dar\Admin\Builder\ListBuilder;
use Dar\Admin\Builder\EditBuilder;
use Dar\Admin\Exceptions\NotModule;
use Symfony\Component\HttpFoundation\Request;

class AdminProvider
{

	/** @var null|AdminContainer */
	protected $container = null;

	/** @var AdminProvider */
	protected static $instance = null;

	/** @var BasePage */
	protected $resource;

	/** @var Request */
	protected $request;

	protected $resourceName = '';

	protected $url = [
		'edit' => '',
		'list' => '',
		'simple' => '',
	];

	protected $baseUrl = 'dar.admin.php';

	const TYPE_PAGE_SIMPLE = 'SIMPLE';
	const TYPE_PAGE_LIST = 'LIST';
	const TYPE_PAGE_EDIT = 'EDIT';

	protected function __construct()
	{
		$this->container = new AdminContainer();
		$this->request = Request::createFromGlobals();
		$this->container->bind('admin.request', function () {
			return $this->request;
		});

	}

	public static function register()
	{
		if (is_null(self::$instance)){
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function addResource($className, $alias = null)
	{
		$resource = new $className;
		$this->container->get('admin.resources')->addResourceStorage($alias, $resource);

//		$this->container->get('admin.resources')->add($resource, $alias);
//		dump(self::$admin->get('admin.resources')->get(SimplePage::class));

		return $this;
	}

	public function setResources($items = [])
	{
		foreach ($items as $code => $item) {
			$this->addResource($item, $code);
		}
	}

	public function initPage()
	{
		/** @var Request $request */
		$request = $this->container->make('admin.request');
		$this->resource = $this->container->get('admin.resources')->resolve($request->get('_resource'));
		$type = strtoupper($request->query->get('_type')) ? : self::TYPE_PAGE_SIMPLE;
		$this->resource->setType($type);

		$uri = $this->makeUrl();

		$uri->setParam('_type', self::TYPE_PAGE_LIST);
		$this->url['list'] = $uri->getUri();

		$uri->setParam('_type', self::TYPE_PAGE_EDIT);
		$this->url['edit'] = $uri->getUri();

		$uri->setParam('_type', self::TYPE_PAGE_SIMPLE);
		$this->url['simple'] = $uri->getUri();

		$this->resource->setUrl($this->url);

		foreach ($this->resource->getModules() as $module) {
			if(!Loader::includeModule($module))
				throw new NotModule('Модуль %s не установлен', $module);
		}

		switch ($type) {
			case self::TYPE_PAGE_EDIT:
				$this->container->bind(IBuilder::class, function () {
					$builder = new EditBuilder($this->resource);
					$builder->createAdminInstance($this->resourceName);

					return $builder;
				});
				break;
			case self::TYPE_PAGE_LIST:
				$this->container->bind(IBuilder::class, function () {
					$builder = new ListBuilder($this->resource);
					$builder->createAdminInstance($this->resourceName);

					return $builder;
				});
				break;
			default:
				$this->container->bind(IBuilder::class, function () {
					$builder = new EditBuilder($this->resource);
					$builder->createAdminInstance($this->resourceName);

					return $builder;
				});
				break;
		}

		return $this->container->make(IBuilder::class);
	}

	public function createPage()
	{
		/** @var \CMain $app */
		$app = $this->container->get('global.app');
		$app->SetTitle($this->resource->getTitle());

		/** @var IBuilder $builder */
		$builder = $this->container->get(IBuilder::class);

		return $builder;
	}

	/**
	 * @method getResourceName - get param resourceName
	 * @return string
	 */
	public function getResourceName()
	{
		return $this->resourceName;
	}

	/**
	 * @method setResourceName - set param ResourceName
	 * @param string $resourceName
	 */
	public function setResourceName($resourceName)
	{
		$this->resourceName = $resourceName;
	}

	/**
	 * @method instance
	 * @return AdminProvider
	 */
	public static function instance()
	{
		return static::register();
	}

	/**
	 * @method makeUrl
	 * @return Uri
	 */
	protected function makeUrl()
	{
		$uri = new Uri($this->baseUrl);
		$uri->addParams([
			'_resource' => $this->resourceName,
			'lang' => LANG,
		]);

		return $uri;
	}

	/**
	 * @method getRequest - get param request
	 * @return Request
	 */
	public static function getRequest()
	{
		return self::$instance->request;
	}

	/**
	 * @method getContainer - get param container
	 * @return AdminContainer|null
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @method getCurrentResource
	 * @return BasePage
	 */
	public function getCurrentResource()
	{
		return $this->resource;
	}

	/**
	 * @method initCurrentResource
	 * @return $this
	 */
	public function initCurrentResource()
	{
		$_resource = $this->request->get('_resource');
		$items = collect(Configuration::readFile());

		if($items->count() > 0){
			Resource::getInstance()->add($items->get($_resource), $_resource);
		}

		return $this;
	}
}
