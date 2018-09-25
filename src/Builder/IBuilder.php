<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 04.09.2018
 */

namespace Dar\Admin\Builder;


use Dar\Admin\IResource;

interface IBuilder
{
	/**
	 * IBuilder constructor.
	 *
	 * @param IResource $resource
	 */
	public function __construct(IResource $resource);

	/**
	 * @method getResource
	 * @return IResource
	 */
	public function getResource(): IResource;

	/**
	 * @method createAdminInstance
	 * @param string $name
	 *
	 * @return IBuilder
	 */
	public function createAdminInstance($name = '');

	public function render();

}