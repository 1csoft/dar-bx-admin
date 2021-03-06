<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 06.09.2018
 */

namespace Dar\Admin;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\ScalarField;
use Dar\Admin\Fields;
use Bitrix\Main\ORM;
use Bitrix\Main;

class AdminSupport
{
	public static $fieldType = [
		Entity\StringField::class => Fields\Input::class,
		Entity\EnumField::class => Fields\Select::class,
		Entity\IntegerField::class => Fields\Number::class,
		Entity\TextField::class => Fields\Textarea::class,
		Entity\FloatField::class => Fields\Number::class,
		Entity\DatetimeField::class => Fields\DateCalendar::class,
		Entity\DateField::class => Fields\DateCalendar::class,

		ORM\Fields\StringField::class => Fields\Input::class,
		ORM\Fields\EnumField::class => Fields\Select::class,
		ORM\Fields\IntegerField::class => Fields\Number::class,
		ORM\Fields\TextField::class => Fields\Textarea::class,
		ORM\Fields\FloatField::class => Fields\Number::class,
		ORM\Fields\DatetimeField::class => Fields\DateCalendar::class,
		ORM\Fields\DateField::class => Fields\DateCalendar::class,
	];

	/**
	 * @method convertToAdminField
	 * @param ScalarField $field
	 * @param string $prefix
	 *
	 * @return Fields\BaseField
	 */
	public static function convertToAdminField($field, $prefix = '')
	{

		$classBX = get_class($field);
		/** @var Fields\BaseField $adminClass */
		$adminClass = self::$fieldType[$classBX];

		if ($adminClass){
			if(strlen($prefix) > 0){
				$name = $prefix.'['.$field->getName().']';
			} else {
				$name = $field->getName();
			}
			$adminField = $adminClass::create($name)
				->required($field->isRequired())
				->label($field->getTitle())
				->filterable();

			if ($field->isRequired()){
				$adminField->isDefault();
			}
			if (!$field instanceof Entity\TextField || !$field instanceof ORM\Fields\TextField){
				$adminField->sortable();
			}
			if ($field instanceof ORM\Fields\EnumField || $field instanceof Entity\EnumField){
				$adminField->options($field->getValues());
			}

			return $adminField;
		}

		return null;
	}

	/**
	 * @method convertToPrimary
	 * @param Entity\Field $field
	 *
	 * @return Fields\Primary
	 */
	public static function convertToPrimary($field)
	{
		return Fields\Primary::create($field->getName())
			->label($field->getTitle())
			->sortable()
			->hideOnCreate();
	}

	/**
	 * @method convertFields
	 * @param Entity\Base $entity
	 * @param string $prefix
	 *
	 * @return Fields\BaseField[]|Fields\Primary[]
	 */
	public static function convertFields(Entity\Base $entity, $prefix = '')
	{
		$primaryList = $entity->getPrimaryArray();
		$items = [];
		foreach ($entity->getFields() as $field) {
			if (!in_array($field->getName(), $primaryList)){
				if ($field instanceof Entity\ScalarField){
					$adminField = static::convertToAdminField($field, $prefix);
					if ($adminField){
						$items[] = $adminField;
					}
				}
			} else {
				$items[] = self::convertToPrimary($field);
			}
		}

		return $items;
	}

	/**
	 * @method prepareGridFilter
	 * @param Entity\Base $entity
	 * @param array $filter
	 *
	 * @return array
	 */
	public static function prepareGridFilter(Entity\Base $entity, $filter = [])
	{
		$result = [];
		foreach ($filter as $code => $val) {
			if ($entity->hasField($code)){
				$result[$code] = $val;
			}
		}

		return $result;
	}

	/**
	 * @method externalJs
	 * @param array $arJs
	 */
	public static function externalJs($arJs = [])
	{
		if (!is_array($arJs))
			$arJs = [$arJs];

		foreach ($arJs as $js) {
			Main\Page\Asset::getInstance()->addJs($js);
		}
	}

	/**
	 * @method externalCss
	 * @param array $css
	 */
	public static function externalCss($css = [])
	{
		if (!is_array($css))
			$css = [$css];

		foreach ($css as $val) {
			AdminContainer::application()->SetAdditionalCSS($val);
		}
	}

	/**
	 * @method externalCoreLib
	 * @param array $lib
	 */
	public static function externalCoreLib($lib = [])
	{
		\CJSCore::Init($lib);
	}

	public static function registerCustomJsLib()
	{
		$jsPath = __DIR__.'/Resources/assets/js';
		$cssPath = __DIR__.'/Resources/assets/css';

		\CJSCore::RegisterExt('admin_fields', [
			'js' => [
				static::getLocalPath($jsPath.'/customFields.js'),
			],
			'css' => [
				static::getLocalPath($jsPath.'/customFields.css'),
				static::getLocalPath($cssPath.'/customFields.css'),
			],
		]);
		\CJSCore::RegisterExt('lodash', ['js' => static::getLocalPath($jsPath.'/lodash.min.js')]);
		\CJSCore::RegisterExt('jquery3', ['js' => 'https://code.jquery.com/jquery-3.3.1.min.js']);
		\CUtil::InitJSCore(['jquery3', 'lodash', 'admin_fields']);
	}

	/**
	 * @method getLocalPath
	 * @param string $path
	 *
	 * @return string
	 */
	public static function getLocalPath(string $path)
	{
		$root = Main\Application::getDocumentRoot();
		if (AdminContainer::getOption('root')){
			$root = AdminContainer::getOption('root');
		}

		return str_replace($root, '', $path);
	}

	/**
	 * @method getUser
	 * @return \CAllUser|\CUser
	 */
	public static function getUser()
	{
		global $USER;
		if (!$USER instanceof \CUser){
			$USER = new \CUser();
		}

		return $USER;
	}
}