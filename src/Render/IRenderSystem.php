<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin\Render;


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
}