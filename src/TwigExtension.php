<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 26.09.2018
 */

namespace Dar\Admin;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
	public function getName()
	{
		return 'dar.admin';
	}

	public function getFunctions()
	{
		return [
			new TwigFunction('ShowMessage', array($this, 'showMessage'), array('message')),
			new TwigFunction('bitrix_sessid_post', array($this, 'bitrix_sessid_post')),
			new TwigFunction('bitrix_sessid_get', array($this, 'bitrix_sessid_get')),
			new TwigFunction('ShowError', array($this, 'showError'), array('message', 'css_class')),
			new TwigFunction('ShowNote', array($this, 'showNote'), array('message', 'css_class')),
			new TwigFunction('IsUserAdmin', array($this, 'isUserAdmin')),
			new TwigFunction('IsUserAuthorized', array($this, 'isUserAuthorized')),
			new TwigFunction('dump', array($this, 'dump')),
			new TwigFunction('dd', array($this, 'dd')),
			new TwigFunction('findUser', array($this, 'findUser')),
			new TwigFunction('phpToJs', array($this, 'phpToJs')),
		];
	}

	public function getGlobals()
	{
		global $APPLICATION;

		return array(
			'APPLICATION' => $APPLICATION,
			'LANG' => LANG,
			'POST_FORM_ACTION_URI' => POST_FORM_ACTION_URI,
			'_REQUEST' => $_REQUEST,
			'SITE_SERVER_NAME' => SITE_SERVER_NAME,
		);
	}

	/**
	 * Возвращает список фильтров, которые будут доступны в шаблоне после добавления данного расширения
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			new TwigFilter('formatDate', array($this, 'formatDate'), array('rawDate', 'format')),
			new TwigFilter('russianPluralForm', array($this, 'russianPluralForm'), array('string', 'count', 'delimiter')),
		);
	}

	//функции, которые используются как функции в твиге
	public function showMessage($message)
	{
		ShowMessage($message);
	}

	public function showError($message, $css_class = "errortext")
	{
		ShowError($message, $css_class);
	}

	public function showNote($message, $css_class = "notetext")
	{
		ShowNote($message, $css_class);
	}

	public function bitrix_sessid_post()
	{
		return bitrix_sessid_post();
	}

	public function bitrix_sessid_get()
	{
		return bitrix_sessid_get();
	}

	public function isUserAdmin()
	{
		global $USER;

		return $USER->IsAdmin();
	}

	public function isUserAuthorized()
	{
		global $USER;

		return $USER->IsAuthorized();
	}

	//функции, которые используются как фильтры в твиге
	public function formatDate($rawDate, $format = 'FULL')
	{
		return FormatDateFromDB($rawDate, $format);
	}

	/**
	 * Получение множественной формы слова в зависимости от числительного перед словом
	 *
	 * @param string $string строка вида 'товар|товара|товаров'
	 * @param string $count числительное
	 * @param string $delimiter разделитель в параметре $string
	 *
	 * @return mixed
	 */
	public function russianPluralForm($string, $count, $delimiter = "|")
	{
		list($endWith1, $endWith2to4, $endWith5to9and0) = explode($delimiter, $string);

		if (strlen($count) > 1 && substr($count, strlen($count) - 2, 1) == "1"){
			return $endWith5to9and0;
		} else {
			$lastDigit = intval(substr($count, strlen($count) - 1, 1));
			if ($lastDigit == 0 || ($lastDigit >= 5 && $lastDigit <= 9)){
				return $endWith5to9and0;
			} elseif ($lastDigit == 1) {
				return $endWith1;
			} else {
				return $endWith2to4;
			}
		}
	}

	public function dump($data = null)
	{
		dump($data);
	}

	public function dd($data = null)
	{
		dd($data);
	}

	public function findUser($name = '', $value = '', $userName = '', $pageName = '')
	{
		echo FindUserID(
			$name, $value, $userName,
			$pageName, "3", "",
			"...", "inputtext", "inputbodybutton"
		);
	}

	public function phpToJs($data = [])
	{
		return \CUtil::PhpToJSObject($data);
	}
}