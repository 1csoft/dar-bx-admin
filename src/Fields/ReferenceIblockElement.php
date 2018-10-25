<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 22.10.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\Uri;
use Soft1c\OrmIblock\ElementTable;

class ReferenceIblockElement extends BaseField
{
	protected $iblockId = null;
	public $displayName = 'NAME';
	protected $displayFormat = '<a href="%s" target="_blank">%s</a>';
	protected $element = null;

	public function __construct($name)
	{
		parent::__construct($name);
		\CJSCore::init("sidepanel");
	}


	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
		$element = $this->getElementById($this->value);

		$element['urlEdit'] = $this->getUrlEdit();
		$element['display_name'] = sprintf($this->displayFormat, $element['urlEdit'], $element[$this->displayName]);

		$tpl = strlen($tpl) > 0 ? $tpl : 'fields/iblockElement.reference';

		$element['event_name'] = $this->getName().'_'.$this->displayName;


		return parent::render($tpl, ['element' => $element]);
	}

	public function renderList($tpl = false, $params = [])
	{
		parent::renderList($tpl, $params);
	}

	/**
	 * @method getIblockId - get param iblockId
	 * @return null
	 */
	public function getIblockId()
	{
		return $this->iblockId;
	}

	/**
	 * @param null $iblockId
	 *
	 * @return ReferenceIblockElement
	 */
	public function iblockId($iblockId)
	{
		$this->iblockId = $iblockId;

		return $this;
	}

	/**
	 * @method getElementById
	 * @param $id
	 *
	 * @return array|null
	 */
	public function getElementById($id)
	{
		if (is_null($this->element)){
			if (is_null($this->iblockId)){
				$this->iblockId = (int)\CIBlockElement::GetIBlockByID($id);
			}

			$this->element = ElementTable::getRow([
				'select' => ['ID', 'NAME', 'CODE', 'XML_ID', 'IBLOCK_ID', 'IBLOCK_TYPE' => 'IBLOCK.IBLOCK_TYPE_ID'],
				'filter' => ['IBLOCK_ID' => $this->iblockId, '=ID' => $id],
			]);
		}

		return $this->element;
	}

	/**
	 * @method getUrlEdit
	 * @return string
	 */
	public function getUrlEdit()
	{
		//iblock_element_edit.php?IBLOCK_ID=147&type=PROGRAMMS_IB&ID=20071776&lang=ru&find_section_section=-1&WF=Y
		return sprintf('iblock_element_edit.php?IBLOCK_ID=%s&type=%s&ID=%d&lang=ru&find_section_section=-1&mode=list',
			$this->element['IBLOCK_ID'], $this->element['IBLOCK_TYPE'], $this->element['ID']
		);
	}

	/**
	 * @method getPopupUrl
	 * @param array $element
	 *
	 * @return string
	 */
	public function getPopupUrl($element = [])
	{
		$uri = new Uri('dar.admin.php?_resource=iblock.elements&lang=ru&_type=LIST');
		$uri->addParams([
			'iblockId' => $this->element['IBLOCK_ID'],
			'eventName' => $this->getName().'_'.$this->displayName
		]);

		return $uri->getUri();
	}
}