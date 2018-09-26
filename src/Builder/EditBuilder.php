<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 04.09.2018
 */

namespace Dar\Admin\Builder;


use Bitrix\Main\DB\Result;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\EventManager;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Dar\Admin\IResource;
use Dar\Admin\Uri;
use Symfony\Component\HttpFoundation\Request;

class EditBuilder extends MainBuilder
{
	/** @var \CAdminForm */
	protected $CAdminTabControl;

	/** @var \CAdminMessage */
	protected $CAdminMessage;

	/** @var \CAdminContextMenu */
	protected $CAdminContextMenu;

	public function __construct(IResource $resource)
	{
		parent::__construct($resource);

		$this->eventManger->addEventHandler('main', 'OnAdminTabControlBegin', function () {
			return $this->onBeforeRenderTabs();
		}, false, 1);
	}

	/**
	 * @method getResource
	 * @return IResource
	 */
	public function getResource(): IResource
	{
		return $this->resource;
	}

	public function createAdminInstance($name = ''): EditBuilder
	{
		if (strlen($name) == 0){
			$name = randString();
		}

		$name = str_replace('.', '_', $name);
		$tabsConfig = [];
		/** @var Tabs $tab */
		foreach ($this->resource->getTabs() as $tab) {
			$tabsConfig[] = $tab->toArray();
		}

		$this->CAdminTabControl = new \CAdminForm($name, $tabsConfig);
		$this->CAdminContextMenu = new \CAdminContextMenu($this->resource->contextEditMenu());
//		$this->CAdminForm = new \CAdminForm();

		$this->saveElement();
		$arElement = $this->getElement()->fetch();
		$this->setFieldValues($arElement);

		return $this;
	}

	public function render()
	{
		$this->CAdminContextMenu->Show();

		$primary = $this->resource->getEntity()->getPrimary();
		$uri = new Uri();
		$uri->setParam($primary, $this->request->get($primary))
			->setParam('lang', LANG);

		$this->CAdminTabControl->Begin([
			"FORM_ACTION" => $uri->getUri()
		]);
		/** @var Tabs $tab */
		foreach ($this->resource->getTabs() as $tab) {
//			$this->CAdminTabControl->BeginNextTab();
			$this->CAdminTabControl->BeginNextFormTab();
			$content = $tab->getContent();
			if (strlen($content) > 0){
				echo $content;
			} else {
				foreach ($tab->getFields() as $field) {
					$this->CAdminTabControl->BeginCustomField($field->getName(), $field->getLabel());
					echo $field->render();
					$this->CAdminTabControl->EndCustomField($field->getName());
				}
			}
		}

		$this->CAdminTabControl->Buttons($this->resource->btnForm());

		$this->CAdminTabControl->Show();
	}

	public function onBeforeRenderTabs()
	{
		AdminSupport::registerCustomJsLib();

	}

	/**
	 * @method saveElement
	 */
	public function saveElement()
	{
		/** @var Request $request */
		$request = AdminContainer::getInstance()->get('admin.request');
		if ($request->isMethod('POST')){
			$data = [
				'post' => $request->request->all(),
				'entity' => [],
				'files' => $request->files->all(),
				'query' => $request->query->all(),
			];
			$entity = $this->resource->getModel();
			foreach ($request->request->all() as $code => $value) {
				if ($entity->hasField($code)){
					$data['entity'][$code] = $this->resource->findField($code)->getValue();
				}
			}

			$this->resource->saveElement($data);
		}
	}

	/**
	 * @method getElement
	 * @return \Bitrix\Main\DB\Result
	 */
	public function getElement()
	{
		$entity = $this->resource->getEntity();
		$primary = $entity->getPrimary();
		$query = new Query($this->resource->getEntity());

		foreach ($this->resource->fields() as $field) {
			$code = $field->getName();
			if ($entity->hasField($code)){
				$query->addSelect($code, $code);
			}
		}
		$primaryValue = $this->request->get($primary);
		$query->where($primary, '=', $primaryValue);
		$query->setLimit(1);

		if(method_exists($this->resource, 'beforeExecElement')){
			$query = $this->resource->beforeExecElement($query);
		}

		$obResult = $query->exec();
		if(method_exists($this->resource, 'beforeFetchElement')){
			/** @var Result $obResult */
			$obResult = $this->resource->beforeFetchElement($obResult);
		}

		return $obResult;
	}

	public function setFieldValues($data = [])
	{
		foreach ($data as $code => $value) {
			$this->resource->fields()->get($code)->value($value);
		}
	}
}