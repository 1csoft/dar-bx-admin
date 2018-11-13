<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 09.11.2018
 */

namespace Dar\Admin\Fields;


use Dar\Admin\Uri;

class ReferenceOrder extends BaseField
{
	public function __construct($name)
	{
		parent::__construct($name);
		\CJSCore::init("sidepanel");
	}

	public function render($tpl = '', $params = [])
	{
		$id = $this->value;
		$element['urlEdit'] = $this->getUrlEdit($id);
		$element['display_name'] = 'sale_order.php?PAGEN_1=1&SIZEN_1=20&lang=ru&set_filter=Y&adm_filter_applied=0&'.$id.'=770689&filter_id_to='.$id;
		$element['display_name'] = sprintf(
			'<a target="_blank" href="sale_order.php?PAGEN_1=1&SIZEN_1=20&lang=ru&set_filter=Y&adm_filter_applied=0&filter_id_from=%d&&filter_id_to=%d">%d</a>',
			$id, $id, $id
		);

		$tpl = strlen($tpl) > 0 ? $tpl : 'fields/referenceOrder';
		$element['event_name'] = $this->getName().'_ID';
		$element['ID'] = $this->value;

		return parent::render($tpl, ['element' => $element]);
	}

	protected function getUrlEdit($id)
	{
		$link = '<a href="sale_order_view.php?ID=%d" target="_blank">%d</a>';

		return sprintf($link, $id, $id);
	}

	public function getPopupUrl()
	{
		$uri = new Uri('dar.admin.php?_resource=order.search&lang=ru&_type=LIST');
		$uri->addParams(['eventName' => $this->getName().'_ID']);

		return $uri->getUri();
	}
}