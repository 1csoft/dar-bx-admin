<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 23.10.2018
 */

namespace Dar\Admin\Fields;


class UserSlideSearch extends UserSearch
{
	public $displayName = 'LOGIN';
	protected $displayFormat = '<a href="%s" target="_blank">%s</a>';

	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
		$arUser = $this->getUserData($this->value);
		$element['event_name'] = $this->name.'_SEARCH';
		$element['urlSearch'] = 'dar.admin.php?_resource=user.search&lang=ru&_type=LIST&eventName='.$element['event_name'];
		$element['urlEdit'] = 'user_edit.php?lang=ru&ID='.$arUser['ID'];
		$element['display_name'] = sprintf($this->displayFormat, $element['urlEdit'], $arUser[$this->displayName]);
		$element['ID'] = $arUser['ID'];

		return parent::render('fields/user.slide.search', ['element' => $element]);
	}


}