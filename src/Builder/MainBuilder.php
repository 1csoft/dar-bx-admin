<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 26.09.2018
 */

namespace Dar\Admin\Builder;


use Dar\Admin\IResource;
use Bitrix\Main\EventManager;
use Dar\Admin\AdminContainer;
use Dar\Admin\BasePage;
use Symfony\Component\HttpFoundation\Request;

class MainBuilder implements IBuilder
{

	/** @var BasePage */
	protected $resource;

	/** @var EventManager */
	protected $eventManger;

	/** @var Request */
	protected $request;
	/**
	 * IBuilder constructor.
	 *
	 * @param IResource $resource
	 */
	public function __construct(IResource $resource)
	{
		$this->resource = $resource;
		$this->eventManger = EventManager::getInstance();
		$this->request = AdminContainer::getRequest();
	}

	/**
	 * @method getResource
	 * @return IResource
	 */
	public function getResource(): IResource
	{
		return $this->resource;
	}

	/**
	 * @method createAdminInstance
	 * @param string $name
	 *
	 * @return IBuilder
	 */
	public function createAdminInstance($name = '')
	{
		return $this;
	}

	public function render()
	{
	}

}