<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.11.2018
 */

namespace Dar\Admin\Fields;


use Bitrix\Main\ORM\Data\DataManager;
use Dar\Admin\BasePage;
use Dar\Admin\Configuration;
use Dar\Admin\Resource;
use Dar\Admin\Uri;

class ReferenceResource extends BaseField
{
	/** @var BasePage */
	protected $ref;

	public function __construct($name)
	{
		parent::__construct($name);
		\CJSCore::init("sidepanel");
	}

	public function render($tpl = '', $params = [])
	{
		$id = $this->value;
		$element['urlEdit'] = $this->getUrlEdit($id);
		$element['display_name'] = sprintf(
			'<a target="_blank" href="%s">%d</a>',
			$this->ref->editLink(), $id
		);
//		dump($this->ref);
		$tpl = strlen($tpl) > 0 ? $tpl : 'fields/referenceOrder';
		$element['event_name'] = $this->getName().'_'.$this->ref->getEntity()->getPrimary();
		$element['ID'] = $this->value;

		return parent::render($tpl, ['element' => $element]);
	}

	protected function getUrlEdit($id)
	{
		$href = $this->ref->editLink().'&'.$this->ref->getEntity()->getPrimary().'='.$id;

		$link = '<a href="%s" target="_blank">%s</a>';

		return sprintf($link, $href, $id);
	}

	public function getPopupUrl()
	{
		$uri = new Uri($this->ref->listLink());
		$uri->addParams(['eventName' => $this->getName().'_'.$this->ref->getEntity()->getPrimary()]);

		return $uri->getUri();
	}

	/**
	 * @method getRef - get param ref
	 * @return BasePage
	 */
	public function getRef()
	{
		return $this->ref;
	}

	/**
	 * @param DataManager|BasePage $ref
	 * @param string $alias
	 *
	 * @return ReferenceResource
	 */
	public function setRef($alias, $ref = '')
	{
		$class = Configuration::findResource($alias);

		if(is_array($class)){
			Resource::getInstance()->add($class['entity'], $alias, $class['modules']);
		} else {
			Resource::getInstance()->add($class, $alias);
		}

		$this->ref = Resource::getInstance()->resolve($alias);

		$this->ref->setUrl([
			'list' => 'dar.admin.php?_resource='.$alias.'&lang=ru&_type=LIST',
			'edit' => 'dar.admin.php?_resource='.$alias.'&lang=ru&_type=EDIT',
		]);

		return $this;
	}

}