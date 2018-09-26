<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 21.09.18
 */

namespace Dar\Admin\Fields;


class FileField extends BaseField
{

	/**
	 * @method render
	 * @param string $tpl
	 * @param array $params
	 *
	 * @return string
	 */
	public function render($tpl = '', $params = [])
	{
		return \Bitrix\Main\UI\FileInput::createInstance(array(
			"name" => $this->getName(),
			"description" => true,
			"upload" => true,
			"allowUpload" => "I",
			"medialib" => true,
			"fileDialog" => true,
			"cloud" => true,
			"delete" => true,
			"maxCount" => 1
		))->show();
	}


}
