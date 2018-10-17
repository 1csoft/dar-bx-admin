<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 04.09.18
 */

namespace Dar\Admin;

use Bitrix\Main;
use Dar\Admin\Builder\ListBuilder;
use Dar\Admin\Builder\Tabs;
use Dar\Admin\Fields\BaseField;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class BasePage implements IResource
{
	/** @var array  */
	protected $modules = [];

	/** @var AdminContainer */
	protected $container;

	/** @var string */
	protected $typeEntity = '';

	/** @var null|string */
	public static $model = null;

	public $extendsHeaders = false;

	protected $url;

	public $versionGrid = 2;

	protected $langMessages = [];

	/** @var Translator */
	protected $Translator;

	/** @var BaseField[]|FieldsCollection */
	public $fields = null;

	protected $fieldMap = [];

	/** @var BaseField[] */
	protected $filterFields = null;

	/** @var Main\Entity\Base */
	protected static $entity = null;

	/** @var null|string */
	protected static $ufId = null;

	/** @var Request */
	protected $request;

	protected $locale = 'ru';
	protected $langPath = __DIR__.'/Resources/lang';

	protected $namePage = '';

	const TYPE_SIMPLE = 'SIMPLE';
	const TYPE_LIST = 'LIST';
	const TYPE_EDIT = 'EDIT';

	public function __construct()
	{
		$this->container = AdminContainer::getInstance();
		$this->typeEntity = static::TYPE_SIMPLE;
		$this->request = AdminContainer::getRequest();

		$this->setLangMessages()->loadResourceMessage();
	}

	/**
	 * @method getContainer - get param container
	 * @return AdminContainer
	 */
	public function getContainer()
	{
		return $this->container;
	}

	public function setType($type = '')
	{
		$this->typeEntity = $type;
	}

	public function getTitle(): string
	{
		return $this->Translator->trans('main.title_page');
	}

	public function getTabs()
	{
		$name = array_pop(explode('\\', static::$model));

		return [
			Tabs::create('edit_'.$name)->name($this->trans('main.tabs.edit'))->setFields($this->fields()),
		];
	}

	/**
	 * @method getModel
	 * @return Main\Entity\Base|null
	 */
	public static function getModel()
	{
		/** @var Main\Entity\DataManager $class */
		$class = static::$model;
		static::$entity = strlen($class) > 0 ? $class::getEntity() : null;

		return static::$entity;
	}

	/**
	 * @method fields
	 * @return FieldsCollection|BaseField[]
	 */
	public function fields()
	{
		if (static::getModel() instanceof Main\Entity\Base && is_null($this->fields)){
			$fields = AdminSupport::convertFields(static::getModel());
			foreach ($fields as $k => $item) {
				$this->fields[$item->getName()] = $item;
				$this->fieldMap[$item->getName()] = $k;
			}
		}

		return new FieldsCollection($this->fields);
	}

	/**
	 * @method isExtendsHeaders
	 *
	 * @return bool
	 */
	public function isExtendsHeaders()
	{
		return $this->extendsHeaders;
	}

	/**
	 * @method getType
	 * @return string
	 */
	public function getType(): string
	{
		return $this->typeEntity;
	}

	/**
	 * @method getUrl - get param url
	 * @param string $name
	 *
	 * @return array|string
	 */
	public function getUrl($name = '')
	{
		return strlen($name) > 0 ? $this->url[$name] : $this->url;
	}

	/**
	 * @method setUrl - set param Url
	 * @param array $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function editLink()
	{
		return $this->url['edit'];
	}

	public function listLink()
	{
		return $this->url['list'];
	}

	public function simpleLink()
	{
		return $this->url['simple'];
	}

	/**
	 * @method contextEditMenu
	 * @return array
	 */
	public function contextEditMenu()
	{
		$menu[] = array(
			"TEXT" => $this->trans('main.context.list'),
			"LINK" => $this->listLink(),
			"TITLE" => $this->trans('main.context.list'),
			"ICON" => "btn_list",
		);

		/** @var Request $request */
		$request = $this->container->get('admin.request');
		$id = $request->query->get('ID') ? : $request->query->get('id') ? : 0;
		if ((int)$id > 0){
			$menu[] = [
				"TEXT" => $this->trans('main.context.new'),
				"TITLE" => $this->trans('main.context.new'),
				"LINK" => $this->editLink().'&ID=0',
				"ICON" => "btn_new",
			];
			$menu[] = array(
				"TEXT" => $this->trans('main.actions.del'),
				"TITLE" => $this->trans('main.actions.del'),
				"LINK" => "javascript:if(confirm(".$this->trans('main.actions.confirm_del').")) ".
					"window.location='rubric_admin.php?ID=".$id."&action=delete⟨=".LANG."&".bitrix_sessid_get()."';",
				"ICON" => "btn_delete",
			);
			$menu[] = array("SEPARATOR" => "Y");
			$menu[] = array(
				"TEXT" => "LINK",
				"TITLE" => "LINK",
				"LINK" => "template_test.php?lang=".LANG."&ID=".$id,
			);
		}

		return $menu;
	}

	/**
	 * @method contextListMenu
	 * @return array
	 */
	public function contextListMenu()
	{
		return [
			array(
				"TEXT" => $this->trans('main.context.new'),
				"LINK" => $this->editLink(),
				"TITLE" => $this->trans('main.context.new'),
				"ICON" => "btn_new",
			),
		];
	}

	/**
	 * @method getLangMessages - get param langMessages
	 * @return array
	 */
	public function getLangMessages()
	{
		return $this->langMessages;
	}

	/**
	 * @method setLangMessages
	 * @return BasePage
	 */
	public function setLangMessages(): BasePage
	{
		$root = Main\Application::getDocumentRoot();
		$FileSystem = new \Illuminate\Filesystem\Filesystem();
		$langMain = $root.'/local/dar/admin/lang';
		if (!$FileSystem->exists($langMain)){
			$langMain = $this->langPath;
		}

		$FileLoader = new FileLoader($FileSystem, $langMain);
		$this->Translator = new Translator($FileLoader, 'ru');

		return $this;
	}

	/**
	 * @method getTranslator - get param Translator
	 * @return Translator
	 */
	public function getTranslator()
	{
		return $this->Translator;
	}

	/**
	 * @method loadResourceMessage
	 * @param string $group
	 * @param string $namespace
	 *
	 * @return Translator
	 */
	public function loadResourceMessage($group = 'main', $namespace = '*')
	{
		$this->Translator->load($namespace, $group, $this->locale);

		return $this->Translator;
	}

	public function filter()
	{
		if (is_null($this->filterFields)){
			$this->filterFields = collect($this->fields())->filter(function (BaseField $el) {
				return $el->isFilterable();
			});
		}

		return $this->filterFields;
	}

	/**
	 * @method getUfId - get param ufId
	 * @return null|string
	 */
	public static function getUfId()
	{
		return self::$ufId;
	}

	/**
	 * @method setUfId - set param UfId
	 * @param null|string $ufId
	 */
	public static function setUfId($ufId)
	{
		self::$ufId = $ufId;
	}

	public function saveElement($data = [])
	{
		if (!empty($data['entity'])){
			$entity = static::getModel();
			$class = $entity->getDataClass();

			$primary = $entity->getPrimary();
			$primaryId = $data['entity'][$primary];
			unset($data['entity'][$primary]);
			$isEdit = (!empty($primaryId));

			if ($isEdit){
				$result = $class::update($primaryId, $data['entity']);
			} else {
				$result = $class::add($data['entity']);
			}

			if ($result->isSuccess()){
				$this->findField($primary)->value($result->getId());
			}
		}
	}

	/**
	 * @method findField
	 * @param $name
	 *
	 * @return BaseField
	 */
	public function findField($name)
	{
		return collect($this->fields())->first(function (BaseField $el) use ($name) {
			return $el->getName() == $name;
		});
	}

	public function actionRow($data = [])
	{
		/** @var \CAdminUiList $lAdmin */
		$lAdmin = AdminContainer::getInstance()->get('CAdminList');

		$uri = new Uri($this->getUrl('edit'));
		$uri->setParam('ID', $data['ID']);

		$actions = [
			array(
				"ICON" => "edit",
				"DEFAULT" => true,
				"TEXT" => $this->trans('main.actions.edit'),
				"ACTION" => $lAdmin->ActionRedirect($uri->getUri()),
			),
		];
		/*$POST_RIGHT = AdminContainer::application()->GetGroupRight('');
		if ($POST_RIGHT>="W"){}*/

		$actions[] = [
			"ICON" => "delete",
			"TEXT" => $this->trans('main.actions.del'),
//			"ACTION"=>"if(confirm('Это правда надо грохнуть?')) ".$lAdmin->actionDoGroup($data['ID'], "delete")
			"ACTION" => $lAdmin->actionDoGroup($data['ID'], "delete"),
		];

		return $actions;
	}

	public function actionRowHandler($name = '', $params = [])
	{
		$actions = [
			'delete' => ['delete', $params],
		];
		$operations = [
			"delete" => true,
			"for_all" => true,
//			"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
//			"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		];
		if ($actions[$name]){
			return call_user_func([$this, $name], $params);
		}

		return false;
	}

	public function groupOperations()
	{
		$operations = [
			"delete" => true,
			"for_all" => false,
			"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
			"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		];

		return $operations;
	}

	public function groupOperationsHandler()
	{
		$operations = $this->groupOperations();
	}

	/**
	 * @method getEntity
	 * @return Main\Entity\Base
	 */
	public function getEntity(): Main\Entity\Base
	{
		return static::getModel();
	}

	/**
	 * @method replaceField
	 * @param $name
	 * @param BaseField $newField
	 */
	public function replaceField($name, BaseField $newField)
	{
		$this->fields->offsetUnset($name);
		$this->fields->offsetSet($name, $newField);
	}

	public function beforeExecList(ListBuilder $adminList = null)
	{
	}

	public function trans($key)
	{
		return $this->getTranslator()->trans($key);
	}

	public function btnForm()
	{
		return array(
			"disabled" => false,
			"btnSave" => true,
			"btnCancel" => true,
			"btnSaveAndAdd" => true,
			'back_url' => $this->getUrl('list')
		);
	}

	/**
	 * @method getNamePage - get param namePage
	 * @return string
	 */
	public function getNamePage()
	{
		return $this->namePage;
	}

	/**
	 * @method setNamePage - set param NamePage
	 * @param string $namePage
	 */
	public function setNamePage($namePage)
	{
		$namePage = str_replace('.', '_', $namePage);
		$this->namePage = $namePage;
	}

	/**
	 * @method getModules - get param modules
	 * @return array
	 */
	public function getModules()
	{
		return $this->modules;
	}

	/**
	 * @method setModules - set param Modules
	 * @param array $modules
	 */
	public function setModules($modules)
	{
		$this->modules = $modules;
	}

//	public function sortItems(Request $request, \CAdminSorting $adminSorting){}
}
