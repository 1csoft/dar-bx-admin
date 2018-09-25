<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 06.09.2018
 */

namespace Dar\Admin;


class Uri extends \Bitrix\Main\Web\Uri
{
	protected $params = [];

	public function __construct($url = '')
	{
		if (strlen($url) == 0){
			$url = AdminContainer::getRequest()->getRequestUri();
		}
		parent::__construct($url);

		$this->parseUrl();
	}


	public function setParam($name, $value)
	{
		$this->params[$name] = $value;

		return $this;
	}

	public function addParams(array $params)
	{
		$this->params = $params;

		return parent::addParams($params);
	}

	public function getUri()
	{
		$this->addParams($this->params);

		return parent::getUri();
	}

	public function removeParam($name)
	{
		if(is_array($name)){
			foreach ($name as $value) {
				unset($this->params[$value]);
			}
		} else {
			unset($this->params[$name]);
		}
		$this->query = http_build_query($this->params, "", "&");
	}

	public function redirect($url = ''){
		if(strlen($url) == 0)
			$url = $this->getUri();

		LocalRedirect($url);
	}

	/**
	 * @method parseUrl
	 */
	protected function parseUrl()
	{
		$currentParams = [];
		if($this->query <> '')
		{
			parse_str($this->query, $currentParams);
		}

		$this->addParams($currentParams);
	}
}