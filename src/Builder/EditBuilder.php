<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 04.09.2018
 */

namespace Dar\Admin\Builder;


use Bitrix\Main\EventManager;
use Dar\Admin\AdminContainer;
use Dar\Admin\AdminSupport;
use Dar\Admin\BasePage;
use Dar\Admin\IResource;
use Symfony\Component\HttpFoundation\Request;

class EditBuilder implements IBuilder
{
	/** @var \CAdminTabControl */
	protected $CAdminTabControl;

	/** @var \CAdminMessage */
	protected $CAdminMessage;

	/** @var \CAdminContextMenu */
	protected $CAdminContextMenu;

	/** @var BasePage */
	protected $resource;

	/** @var EventManager */
	protected $eventManger;

	public function __construct(IResource $resource)
	{
		$this->resource = $resource;
		$this->eventManger = EventManager::getInstance();

		$this->eventManger->addEventHandler('main', 'OnAdminTabControlBegin', function (){
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
		if(strlen($name) == 0){
			$name = randString();
		}
		$tabsConfig = [];
		/** @var Tabs $tab */
		foreach ($this->resource->getTabs() as $tab) {
			$tabsConfig[] = $tab->toArray();
		}

		$this->CAdminTabControl = new \CAdminTabControl($name, $tabsConfig);
		$this->CAdminContextMenu = new \CAdminContextMenu($this->resource->contextEditMenu());

		$this->saveElement();

		return $this;
	}

	public function render()
	{
		$this->CAdminContextMenu->Show();

		echo '<form action="'.POST_FORM_ACTION_URI.'" name="form_edit" method="post">';
		$this->CAdminTabControl->Begin();
		/** @var Tabs $tab */
		foreach ($this->resource->getTabs() as $tab) {
			$this->CAdminTabControl->BeginNextTab();
			$content = $tab->getContent();
			if(strlen($content) > 0){
				echo $content;
			} else {
				foreach ($tab->getFields() as $field) {
					echo $field->render();
				}
			}
		}

		$this->CAdminTabControl->Buttons(
			array(
				"back_url"=>$this->resource->getUrl('list'),
			)
		);
		$this->CAdminTabControl->End();
		echo '</form>';
	}

	public function onBeforeRenderTabs()
	{
		AdminSupport::registerCustomJsLib();
//		AdminSupport::externalCoreLib();
//		AdminSupport::externalCss();
//		AdminSupport::externalJs();

	}

	public function saveElement()
	{
		/** @var Request $request */
		$request = AdminContainer::getInstance()->get('admin.request');
		if($request->isMethod('POST')){
			$data = [
				'post' => $request->request->all(),
				'entity' => [],
				'files' => $request->files->all(),
				'query' => $request->query->all()
			];
			$entity = $this->resource->getModel();
			foreach ($request->request->all() as $code => $value) {
				if($entity->hasField($code)){
					$data['entity'][$code] = $this->resource->findField($code)->getValue();
				}
			}
			$this->resource->saveElement($data);
		}

	}
}