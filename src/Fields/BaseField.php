<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Fields;


use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Illuminate\View\Factory;
use Bitrix\Main\EventManager;

abstract class BaseField
{

	/** @var string|null */
	protected $name = null;

	/** @var bool */
	protected $required = false;

	/** @var string */
	protected $class = '';

	/** @var string */
	protected $label = '';

	/** @var array */
	protected $attr = [];

	/** @var mixed */
	protected $value;

	/** @var BaseField */
	protected static $instance = null;

	protected $sortable;

	protected $default;

	protected $hideOnCreate = false;

	protected $onlyOnList = false;

	protected $onlyOnDetail = false;

	protected $hideAll = false;

	protected $filterable;

	/** @var bool  */
	protected $disabled = false;

	/** @var string */
	protected $tpl;

	/** @var EventManager */
	protected $eventManger;

	/** @var array  */
	protected $options = [];

	protected $tab;

	protected function __construct($name = null)
	{
		$this->name = $name;
		$this->eventManger = EventManager::getInstance();

		$this->eventManger->addEventHandler('main', 'OnAdminTabControlBegin', function (){
			$this->onBeforeRenderField();
		}, false, 1);

		if(AdminContainer::getRequest()->request->has($name)){
			$this->value(AdminContainer::getRequest()->request->get($name));
		}
	}

	/**
	 * @method create
	 * @param $name
	 *
	 * @return BaseField
	 */
	public static function create($name)
	{
		return new static($name);
	}

	/**
	 * @method getName - get param name
	 * @return string|null
	 */
	public function getName()
	{
		return $this->name;
	}

	public function required($flag = true)
	{
		$this->required = $flag;

		return $this;
	}

	/**
	 * @method isRequired - get param required
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @method getClass - get param class
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 *
	 * @return BaseField
	 */
	public function styleClass(string $class): BaseField
	{
		$this->class = $class;

		return $this;
	}

	/**
	 * @method getLabel - get param label
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 *
	 * @return BaseField
	 */
	public function label(string $label): BaseField
	{
//		$label = Encoding::convertEncoding($label, 'cp1251', 'utf-8');
		$this->label = $label;

		return $this;
	}

	/**
	 * @method getAttr - get param attr
	 * @return array
	 */
	public function getAttr()
	{
		return $this->attr;
	}

	/**
	 * @param array $attr
	 *
	 * @return BaseField
	 */
	public function attr(array $attr): BaseField
	{
		$this->attr = $attr;

		return $this;
	}

	/**
	 * @method getValue - get param value
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 *
	 * @return BaseField
	 */
	public function value($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * @method getInstance
	 * @param $name
	 *
	 * @return BaseField
	 */
	public static function getInstance($name): BaseField
	{
		if(is_null(self::$instance)){
			self::$instance = new static($name);
		}

		return self::$instance;
	}

	/**
	 * @method setInstance - set param Instance
	 * @param BaseField $instance
	 */
	public static function setInstance(BaseField $instance)
	{
		self::$instance = $instance;
	}


	/**
	 * @method template
	 * @param $tpl
	 *
	 * @return $this
	 */
	public function template($tpl)
	{
		$this->tpl = $tpl;

		return $this;
	}

	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
		if(strlen($tpl) == 0){
			$tpl = $this->tpl;
		}
		if (strlen($tpl) > 0){
			$view = AdminContainer::getInstance()->get('admin.view');
			if(AdminContainer::getInstance()->getOption('templateSystem') == 'twig'){
				$tpl .= '.twig';
//				$tpl = __DIR__.'/../Resources/twig/'.$tpl;
			} else {
				$tpl .= '.blade.php';
				$tpl = __DIR__.'/../Resources/blade/'.$tpl;
			}

			$context = ['item' => $this];
			if(!is_array($params))
				$params = [];

			$context['value'] = $this->getValue();
			$context['name'] = $this->getName();
			$context['class'] = $this->getClass();
			$context['options'] = $this->getOptions();
			$context['label'] = $this->getLabel();

			$context += $params;

			$this->onBeforeRenderField();

			if(AdminContainer::getInstance()->getOption('templateSystem') == 'twig'){
				return $view->render($tpl, $context);
			} else {
				return $view->file($tpl, $context)->render();
			}
		}

		return '';
	}

	/**
	 * @method isSortable - get param sortable
	 * @return bool
	 */
	public function isSortable()
	{
		return $this->sortable;
	}

	/**
	 * @param mixed $sortable
	 *
	 * @return BaseField
	 */
	public function sortable($sortable = true)
	{
		$this->sortable = $sortable;

		return $this;
	}

	/**
	 * @method getDefault - get param default
	 * @return bool
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * @param mixed $default
	 *
	 * @return BaseField
	 */
	public function isDefault($default = true)
	{
		$this->default = $default;

		return $this;
	}

	/**
	 * @method getHideOnCreate - get param hideOnCreate
	 * @return bool
	 */
	public function getHideOnCreate()
	{
		return $this->hideOnCreate;
	}

	/**
	 * @param bool $hideOnCreate
	 *
	 * @return BaseField
	 */
	public function hideOnCreate(bool $hideOnCreate = true): BaseField
	{
		$this->hideOnCreate = $hideOnCreate;

		return $this;
	}

	/**
	 * @method onlyOnDetail
	 * @param bool $onlyOnDetail
	 *
	 * @return $this
	 */
	public function onlyOnDetail($onlyOnDetail = true)
	{
		$this->onlyOnDetail = $onlyOnDetail;

		return $this;
	}

	/**
	 * @method onlyOnList
	 * @param bool $onlyOnList
	 *
	 * @return $this
	 */
	public function onlyOnList($onlyOnList = true)
	{
		$this->onlyOnList = $onlyOnList;

		return $this;
	}

	/**
	 * @method isFilterable - get param filterable
	 * @return bool
	 */
	public function isFilterable()
	{
		return $this->filterable;
	}

	/**
	 * @method filterable
	 * @param bool $filterable
	 *
	 * @return $this
	 */
	public function filterable($filterable = true)
	{
		$this->filterable = $filterable;

		return $this;
	}

	/**
	 * @method onBeforeRenderField
	 */
	public function onBeforeRenderField()
	{
//		\CJSCore::Init(['jquery', 'lodash', 'admin_fields']);
	}

	/**
	 * @method getOptions - get param options
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @method setOptions - set param Options
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}

	/**
	 * @method addOption
	 * @param $name
	 * @param $v
	 *
	 * @return $this
	 */
	public function addOption($name, $v)
	{
		$this->options[$name] = $v;

		return $this;
	}

	/**
	 * @method hideAll
	 * @param bool $v
	 */
	public function hideAll($v = true)
	{
		$this->hideAll = $v;
	}

	/**
	 * @method isHiddenAll
	 * @return bool
	 */
	public function isHiddenAll()
	{
		return $this->hideAll;
	}

	/**
	 * @method disabled
	 * @param bool $v
	 */
	public function disabled($v = true)
	{
		$this->disabled = $v;
	}

	/**
	 * @method isDisabled
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	public function convertEncodingToCurrent($str = '')
	{
		if(mb_detect_encoding($str) == 'UTF-8' && !Application::isUtfMode()){
			$str = Encoding::convertEncoding($str, 'UTF-8', 'WINDOWS-1251');
//			$str = iconv('UTF-8', 'WINDOWS-1251', $str);
		}
		return $str;
	}

	public function tab($tabName = '')
	{
		$this->tab = $tabName;
	}
}