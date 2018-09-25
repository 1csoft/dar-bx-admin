<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 04.09.2018
 */

namespace Dar\Admin\Builder;


use Dar\Admin\AdminContainer;
use Dar\Admin\AdminProvider;
use Dar\Admin\Fields\BaseField;
use Dar\Admin\FieldsCollection;
use Dar\Admin\IResource;

class Tabs
{
	protected $id;
	protected $name;
	protected $title;
	protected $icon;
	protected $content;

	/** @var BaseField[] */
	protected $fields;

	public static function create(string $id)
	{
		$tab = new static();
		$tab->setId($id);

		return $tab;
	}

	/**
	 * @method getId - get param id
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @method setId - set param Id
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	public function name($name)
	{
		$this->name = $name;

		return $this;
	}

	public function title($title = '')
	{
		if (strlen($title) == 0 && $this->name){
			$title = $this->name;
		}
		$this->title = $title;

		return $this;
	}

	public function icon($icon = '')
	{
		$this->icon = $icon;
	}

	/**
	 * @method getName - get param name
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @method getTitle - get param title
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @method getIcon - get param icon
	 * @return mixed
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	public function toArray()
	{
		return [
			'DIV' => $this->getId(),
			'TAB' => $this->getName(),
			'ICON' => $this->getIcon(),
			'TITLE' => $this->getTitle()
		];
	}

	/**
	 * @method getContent - get param content
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param mixed $content
	 *
	 * @return Tabs
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * @method setComponent
	 * @param $name
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return Tabs
	 */
	public function setComponent($name, $tpl = '', $params = [])
	{
		ob_start();
		AdminContainer::getInstance()->get('global.app')->IncludeComponent($name, $tpl, $params, false);
		$content = ob_get_contents();
		ob_end_clean();

		return $this->setContent($content);
	}

	/**
	 * @method setFields
	 * @param BaseField[]|FieldsCollection $fields
	 *
	 * @return $this
	 */
	public function setFields($fields = [])
	{
		foreach ($fields as $code => $field) {
			if(!$field->getHideOnCreate()){
				$this->fields[$code] = $field;
			}
		}

		return $this;
	}

	/**
	 * @method getFields - get param fields
	 * @return BaseField[]|FieldsCollection
	 */
	public function getFields()
	{
		return $this->fields;
	}

}