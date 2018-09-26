<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

namespace Dar\Admin;


use Arrilot\BitrixBlade\BladeProvider;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class AdminContainer extends \Illuminate\Container\Container
{

	/** @var array  */
	protected $options = [
		'templateSystem' => 'twig',
		'jsPath' => __DIR__.'/Resources/js',
		'cssPath' => __DIR__.'/Resources/css',
		'root' => false
	];

	public function __construct()
	{
		global $APPLICATION, $USER, $USER_FIELD_MANAGER;

		$this->instance('global.app', $APPLICATION);
		$this->instance('global.user', $USER);
		$this->instance('global.uf', $USER_FIELD_MANAGER);
		$this->instance('admin.resources', Resource::getInstance());

		BladeProvider::register(__DIR__.'/Resources/blade');
		$this->instance('admin.blade', BladeProvider::getViewFactory());


		$this->singleton('admin.twig', function (){
			$twigLoader = $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/Resources/twig');
			$twig = new \Twig\Environment($twigLoader, [
				'cache' => $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/twig_templates',
				'charset' => Application::isUtfMode() ? 'utf-8' : 'cp1251',
				'auto_reload' => true,
				'debug' => true,
			]);
			$twig->addExtension(new \Twig\Extension\DebugExtension());
			$twig->addExtension(new TwigExtension());

			return $twig;
		});
		$this->singleton('admin.view', function (AdminContainer $container) {
			if($this->options['templateSystem'] == 'twig'){
				return $container->get('admin.twig');
			} else {
				return $container->get('admin.blade');
			}
		});

		$this->options['root'] = $this->options['root'] ?: $_SERVER['DOCUMENT_ROOT'];

		$extraSettings = Configuration::getValue('dar.admin');
		if($extraSettings['root']){
			$this->options['root'] = $extraSettings['root'];
		}
		self::$instance = $this;
	}



	/**
	 * @method getOptions - get param options
	 * @return Collection
	 */
	public function getOptions()
	{
		return collect($this->options);
	}

	/**
	 * @method getOption
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function getOption($name)
	{
		return self::getInstance()->getOptions()->get($name);
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
