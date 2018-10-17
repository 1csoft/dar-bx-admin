<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.10.2018
 */

namespace Dar\Admin\Render;


use Dar\Admin\AdminContainer;

class TwigSystem implements IRenderSystem
{

	protected $twig;

	/**
	 * TwigSystem constructor.
	 */
	public function __construct()
	{
		$twigLoader = new \Twig\Loader\FilesystemLoader(__DIR__.'/Resources/twig');
		$this->twig = new \Twig\Environment($twigLoader, [
			'cache' => $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/twig_templates',
			'charset' => Application::isUtfMode() ? 'utf-8' : 'cp1251',
			'auto_reload' => true,
			'debug' => true,
		]);
		$this->twig->addExtension(new \Twig\Extension\DebugExtension());
		$this->twig->addExtension(new TwigExtension());

		AdminContainer::getInstance()->singleton('admin.twig', $this->twig);

		unset($twigLoader);
	}


	/**
	 * @method view
	 * @param string $templateName
	 * @param array $context
	 *
	 * @return string
	 */
	public function view(string $templateName, $context = []): string
	{
		return $this->twig->render($templateName, $context);
	}


}