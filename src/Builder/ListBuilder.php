<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 05.09.2018
 */

namespace Dar\Admin\Builder;


use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Dar\Admin\Fields\Primary;
use Dar\Admin\IResource;
use Bitrix\Main;
use Symfony\Component\HttpFoundation\Request;

class ListBuilder implements IBuilderLIst
{
	/** @var BasePage */
	protected $resource;

	/** @var \CAdminUiList */
	protected $CAdminList;

	/** @var \CAdminSorting */
	protected $CAdminSorting;

	/** @var \CAdminFilter */
	protected $CAdminFilter;

	/** @var Main\Entity\Base */
	protected $entity;

	protected $urls = [];

	protected $filterData = [];

	protected $nameInitTable;

	protected $adminFilter = [];

	/** @var Main\Entity\Query */
	protected $query;

	/** @var AdminContainer */
	protected $container;

	protected $actions = [];

	/** @var Request */
	protected $request;

	public function __construct(IResource $resource)
	{
		$this->resource = $resource;
		$this->container = AdminContainer::getInstance();
		$this->request = $this->container->get('admin.request');
	}

	/**
	 * @method getResource
	 * @return IResource
	 */
	public function getResource(): IResource
	{
		return $this->resource;
	}

	public function createAdminInstance($name = '')
	{
		$this->nameInitTable = str_replace('.', '_', $name);

		$this->CAdminSorting = new \CAdminSorting($this->nameInitTable, 'ID', 'DESC');

		if ($this->resource->versionGrid == 2){
			$this->CAdminList = new \CAdminUiList($this->nameInitTable, $this->CAdminSorting);
		} else {
			$this->CAdminList = new \CAdminList($this->nameInitTable, $this->CAdminSorting);
		}

		$this->container->instance('CAdminList', $this->CAdminList);

		$this->entity = $this->resource->getEntity();

		$this->resource->setLangMessages();

		$operations = $this->resource->groupOperations();

		$defaultActions = [
			'activate', 'deactivate', 'delete'
		];
		$actions = [];
		foreach ($operations as $code => $operation) {
			if($operation instanceof \Closure){
				$this->actions[$code] = $operation;
				$actions[$code] = true;
			} elseif(is_bool($operation)){
				$actions[$code] = $operation;
				if($operation === true && in_array($code, $defaultActions))
					$this->actions[$code] = function ($arIds, $isAll) use($code){
						return $this->{$code."GroupAction"}($arIds, $isAll);
					};
			}
		}

		$this->CAdminList->AddGroupActionTable($actions);

		$this->actionsRowHandler();

		$this->initHeadersTable();

		$this->initFilter();

		$this->resource->beforeExecList($this);

		$this->exec();

		/*foreach ($this->resource->filter() as $item) {
		}*/
	}

	public function render()
	{
		if($this->CAdminList instanceof \CAdminUiList){
			$linkExcel = \CHTTP::urlAddParams(AdminContainer::application()->GetCurPageParam(), array("mode" => "excel"));
			$aAdditionalMenu[] = array(
				"TEXT" => "Excel",
				"TITLE" => GetMessage("admin_lib_excel"),
				"LINK" => $linkExcel,
				"GLOBAL_ICON"=>"adm-menu-excel",
			);
			$this->CAdminList->context = new UiContextMenu($this->resource->contextListMenu(), $aAdditionalMenu);
		} else {
			$this->CAdminList->AddAdminContextMenu($this->resource->contextListMenu());
		}
		$this->CAdminList->CheckListMode();
		$this->renderFilter();
		$this->CAdminList->DisplayList();
	}

	public function exec()
	{
		$nav = new Main\UI\PageNavigation("pages-import-admin");
		$nav->setPageSize($this->CAdminList->getNavSize());
		$nav->initFromUri();

		$filterOption = new Main\UI\Filter\Options($this->nameInitTable);
		$filterData = $filterOption->getFilter($this->filterData);

		$entity = $this->resource->getModel();
		$filter = AdminSupport::prepareGridFilter($entity, $filterData);

		$this->query = new Main\Entity\Query($this->resource->getModel());
		foreach ($filter as $k => $v) {
			$this->query->addFilter($k, $v);
		}
		foreach ($this->CAdminList->GetVisibleHeaderColumns() as $column) {
			$this->query->addSelect($column);
		}
		$this->query->setOffset($nav->getOffset());
		$this->query->countTotal(true);

		$this->query->setLimit($nav->getPageSize());

		$obItems = $this->query->exec();
		$nav->setRecordCount($obItems->getCount());
		$this->CAdminList->setNavigation($nav, 'Страница', false);

		$ufId = $this->resource::getUfId();
		while ($item = $obItems->fetch()) {
			$ID = $item["ID"];
			$row =& $this->CAdminList->addRow($ID, $item);
			foreach ($item as $k => $value) {
				$row->addViewField($k, $value);
			}

			if (!is_null($ufId)){
				AdminContainer::userFieldManager()->addUserFields($ufId, $item, $row);
			}

			$row->AddActions($this->resource->actionRow($item));
		}

	}

	/**
	 * @method initHeadersTable
	 */
	protected function initHeadersTable()
	{
		$arHeaders = [];
		foreach ($this->resource->fields() as $head) {
			if($head->isHiddenAll())
				continue;

			$item = [
				'id' => $head->getName(),
				'content' => $head->getLabel(),
				'default' => $head->getDefault() || $head instanceof Primary,
			];
			if ($head->isSortable()){
				$item['sort'] = $head->getName();
			}

			$arHeaders[] = $item;
		}
		$this->CAdminList->AddHeaders($arHeaders);
	}

	public function initFilter()
	{
		$filterRender = $filterQuery = [];
		$filter = $this->resource->filter();
		foreach ($filter as $field) {
			$filterQuery[$field->getName()] = $field->getName();
			$filterRender[] = $field->getLabel();

			if ($this->resource->versionGrid == 2){
				$this->filterData[] = [
					'id' => $field->getName(),
					'name' => $field->getLabel(),
					'filterable' => true,
					"default" => true,
				];
				/*$filterFields = array(
					array(
						"id" => "ACTIVE",
						"name" => GetMessage("F_ACTIVE"),
						"type" => "list",
						"items" => array(
							"Y" => GetMessage("MAIN_YES"),
							"N" => GetMessage("MAIN_NO")
						),
						"filterable" => ""
					),
				);*/
			}
		}

		switch ($this->resource->versionGrid) {
			case 2:
				$arFilter = [];
				$this->CAdminList->AddFilter($this->filterData, $arFilter);

				if (!is_null($this->resource::getUfId())){
					AdminContainer::userFieldManager()->AdminListAddFilterV2(
						$this->resource::getUfId(), $arFilter, $this->nameInitTable, $this->filterData
					);
				}
				break;
			case 1:
			default:
				$this->CAdminList->InitFilter($filterQuery);
				break;
		}


		$this->CAdminFilter = new \CAdminFilter($this->nameInitTable.'_filter', $filterRender);
	}

	public function renderFilter()
	{
		switch ($this->resource->versionGrid) {
			case 2:
				$this->CAdminList->DisplayFilter($this->filterData);
				if (!is_null($this->resource::getUfId())){
					AdminContainer::userFieldManager()->AdminListAddFilterFieldsV2(
						$this->resource::getUfId(), $this->filterData
					);
				}

				break;
			case 1:
			default:
				$this->showOldFilter();

				break;
		}
	}

	public function showOldFilter()
	{
		echo '<form name="find_form" method="get" action="">';
		$this->CAdminFilter->Begin();
		foreach ($this->resource->filter() as $item) {
			echo $item->render();
//					echo '
//					<tr>
//  <td> '.$item->getLabel().':</td>
//  <td>
//    <input type="text" name="'.$item->getName().'" size="47" value="">
//  </td>
//</tr>
//					';
		}


//				dump($this->resource->filter());
		$this->CAdminFilter->Buttons([
			'table_id' => $this->nameInitTable,
			'url' => AdminContainer::application()->GetCurPage(),
			'form' => 'find_form',
		]);
		$this->CAdminFilter->End();
		echo '</form>';
	}

	/**
	 * @method getNameGrid - get param nameInitTable
	 * @return mixed
	 */
	public function getNameGrid()
	{
		return $this->nameInitTable;
	}

	/**
	 * @method getFilterData - get param filterData
	 * @return array
	 */
	public function getFilterData()
	{
		return $this->filterData;
	}

	public function actionsRowHandler()
	{
		if($arID = $this->CAdminList->GroupAction()){
			$request = AdminProvider::getRequest();
			$gridId = $request->query->get('grid_id');
			$isAllRows = $request->request->get('action_all_rows_'.$gridId);
			$action = $request->request->get('action_button_'.$gridId);

			if( $this->actions[$action]){
				$this->actions[$action]($arID, $isAllRows == 'N' ? false : true);
			}
		}
	}

	protected function deleteGroupAction($arIds = [], $isAll = false)
	{
		$dataClass = $this->resource->getModel()->getDataClass();
		foreach ($arIds as $id) {
			$dataClass::delete($id);
		}
	}
}
