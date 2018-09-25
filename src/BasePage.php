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
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class BasePage implements IResource
{

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

	const TYPE_SIMPLE = 'SIMPLE';
	const TYPE_LIST = 'LIST';
	const TYPE_EDIT = 'EDIT';

	public function __construct()
	{
		$this->container = AdminContainer::getInstance();
		$this->typeEntity = static::TYPE_SIMPLE;
		$this->request = AdminContainer::getRequest();
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
		return 'Админская страница';
	}

	public function getTabs()
	{
		$name = array_pop(explode('\\', static::$model));

		return [
			Tabs::create('edit_'.$name)->name('Редактировать элемент')->setFields($this->fields()),
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
			/*foreach ($fields as $item) {
				$k = 'admin::fields.list_'.$item->getName();
				$msg = $this->loadResourceMessage()->trans($k);
				if ($item->getLabel() === $item->getName() && $k !== $msg){
					$item->label($msg);
				}
			}*/
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
			"TEXT" => 'Список элементов',
			"LINK" => $this->listLink(),
			"TITLE" => 'Список элементов',
			"ICON" => "btn_list",
		);

		/** @var Request $request */
		$request = $this->container->get('admin.request');
		$id = $request->query->get('ID') ? : $request->query->get('id') ? : 0;
		if ((int)$id > 0){
			$menu[] = [
				"TEXT" => 'Добавить новый элемент',
				"TITLE" => 'Добавить новый элемент',
				"LINK" => $this->editLink().'&ID=0',
				"ICON" => "btn_new",
			];
			$menu[] = array(
				"TEXT" => 'Удалить',
				"TITLE" => 'Удалить',
				"LINK" => "javascript:if(confirm('Это правда надо удалять?')) ".
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
				"TEXT" => 'Добавить элемент',
				"LINK" => $this->editLink(),
				"TITLE" => 'Добавить элемент',
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
	 * @param array $langMessages
	 *
	 * @return BasePage
	 */
	public function setLangMessages($langMessages = []): BasePage
	{
		$this->langMessages = $langMessages;

		$ArrayLoader = new ArrayLoader();
		$ArrayLoader->addMessages('ru', 'fields', $langMessages, 'admin');
		$this->Translator = new Translator($ArrayLoader, 'ru');

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
	 * @return Translator
	 */
	public function loadResourceMessage()
	{
		$this->Translator->load('admin', 'fields', 'ru');

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
				"TEXT" => 'Изменить',
				"ACTION" => $lAdmin->ActionRedirect($uri->getUri()),
			),
		];
		/*$POST_RIGHT = AdminContainer::application()->GetGroupRight('');
		if ($POST_RIGHT>="W"){}*/

		$actions[] = [
			"ICON" => "delete",
			"TEXT" => 'Удалить',
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
}