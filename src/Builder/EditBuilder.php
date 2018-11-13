<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 04.09.2018
 */

namespace Dar\Admin\Builder;


use Bitrix\Main\DB\Result;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Entity\ScalarField;
use Bitrix\Main\Page\Asset;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Dar\Admin\Exceptions\NotFoundResource;
use Dar\Admin\Fields\HideInput;
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

	protected $includeModels;

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

		$this->saveElement();

		$primary = $this->resource->getEntity()->getPrimary();
		if($this->request->query->has($primary)){
			$arElement = $this->getElement()->fetch();
			if (!$arElement){
				throw new NotFoundResource('Ёлемент не найден');
			}

			$this->setFieldValues($arElement);
			if($arElement){
				$this->includeModels = $this->getIncludes($arElement);
			}
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
			"FORM_ACTION" => $uri->getUri(),
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

	/**
	 * @method onBeforeRenderTabs
	 */
	public function onBeforeRenderTabs()
	{
		AdminSupport::registerCustomJsLib();

		foreach ($this->resource->addExternalCss()['EDIT'] as $css) {
			AdminContainer::application()->SetAdditionalCSS($css);
		}
		foreach ($this->resource->addExternalJs()['EDIT'] as $js) {
			Asset::getInstance()->addJs($js);
		}
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
				'entity' => [],
				'files' => $request->files->all(),
				'query' => $request->query->all(),
				'post' => $request->request->all(),
			];

			$entity = $this->resource->getModel();
			$resourceFields = $this->resource->fields();
			foreach ($resourceFields as $field) {
				if($entity->getField($field->getName()) && $entity->getField($field->getName()) instanceof ScalarField){
					$data['entity'][$field->getName()] = $field->getValue();
				}
			}

			$this->resource->saveElement($data);
			/*collect($this->resource->getTabs())->each(function (Tabs $tabs) use ($data){
				if(!empty($tabs->getRelationship())){
					$refResource = $this->includeModels;


					$refEntity = $refResource->getEntity();
					$refData = $data;
					$refData['entity'] = [];
					foreach ($refResource->fields() as $cc => $vv) {
						if($refEntity->getField($vv->getName()) && $refEntity->getField($vv->getName()) instanceof ScalarField){
							$refData['entity'][$vv->getName()] = $vv->getValue();
							//$refData['entity'][$vv->getName()] = $this->request->request->get($cc);
						}
					}

					$relation = collect($this->resource->getRelationships())->first(function ($el) use ($refResource){
						return $el['model'] == get_class($refResource);
					});
					$refValue = $this->resource->fields()->get($relation['this'])->getValue();
					$refData['entity'][$relation['this']] = $refValue;

					$refResource->fields()->get($relation['this'])->value($refValue);
					$refResource->saveElement($refData);

				}
			});*/

			if($request->request->has('apply')){
				$primary = $this->resource->getEntity()->getPrimary();
				$id = $this->resource->findField($primary)->getValue();
				LocalRedirect($this->resource->editLink($id));
			}

			if($request->request->has('save')){
				LocalRedirect($this->resource->listLink());
			}

			if($request->request->has('save_and_add')){
				LocalRedirect($this->resource->editLink());
			}
		}
	}

	/**
	 * @method getElement
	 * @param BasePage|null $resource
	 * @param array $filter
	 *
	 * @return \Bitrix\Main\DB\Result
	 */
	public function getElement(BasePage $resource = null, $filter = [])
	{
		if(!$resource instanceof IResource){
			$resource = $this->resource;
		}
		$entity = $resource->getEntity();
		$primary = $entity->getPrimary();
		$query = new Query($entity);

		foreach ($resource->fields() as $field) {
			$code = $field->getName();
			if ($entity->hasField($code)){
				$query->addSelect($code, $code);
			}
		}

		$primaryValue = $this->request->get($primary);
		if ($entity->getField($primary) instanceof IntegerField){
			$primaryValue = (int)$primaryValue;
		}
		if(count($filter) > 0){
			$query->setFilter($filter);
		} else {
			$query->where($primary, '=', $primaryValue);
		}

		$query->setLimit(1);

		if (method_exists($resource, 'beforeExecElement')){
			$query = $resource->beforeExecElement($query);
		}

		$obResult = $query->exec();
		if (method_exists($resource, 'beforeFetchElement')){
			/** @var Result $obResult */
			$obResult = $resource->beforeFetchElement($obResult);
		}

		return $obResult;
	}

	public function setFieldValues($data = [], BasePage $resource = null)
	{
		if(!$resource instanceof IResource){
			$resource = $this->resource;
		}

		foreach ($data as $code => $value) {
			$resource->fields()->get($code)->value($value);
		}
	}

	/**
	 * @method getIncludes
	 * @param $primaryData
	 *
	 * @return BasePage[]
	 */
	public function getIncludes($primaryData)
	{
		$includes = [];
		foreach ($this->resource->getRelationships() as $relationship) {

			/** @var BasePage $BasePage */
			$BasePage = $this->resource->getContainer()->make($relationship['model']);

			$name = str_replace('\\', '_', $relationship['model']);
			$BasePage->fields()->add(HideInput::create($name)->value($name));

			$obItem = $this->getElement($BasePage, [
				$relationship['this'] => $primaryData[$relationship['this']],
			]);

			$data = $obItem->fetch();
			$this->setFieldValues($data, $BasePage);

			$data['type'] = $relationship['model'];

			$includes[] = $BasePage;
		}

		return $includes;
	}
}