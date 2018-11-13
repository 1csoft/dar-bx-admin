<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin\Render;


use Illuminate\Support\Collection;

interface IRenderSystem
{
	/**
	 * @method view
	 * @param string $templateName
	 * @param array $context
	 *
	 * @return string
	 */
	public function view(string $templateName, $context = []): string;

	/**
	 * @method getExtension
	 * @return Collection
	 */
	public function getExtension(): Collection;

	public function getViewSystem();
}