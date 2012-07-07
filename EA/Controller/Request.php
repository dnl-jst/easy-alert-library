<?php

class EA_Controller_Request
{
	protected $sApplicationPath;
	protected $sControllerName;
	protected $sActionName;
	protected $aParams;

	public function setApplicationPath($sApplicationPath)
	{
		$this->sApplicationPath = (string) $sApplicationPath;
	}

	public function getApplicationPath()
	{
		return $this->sApplicationPath;
	}

	public function setControllerName($sControllerName)
	{
		$this->sControllerName = (string) $sControllerName;
	}

	public function getControllerName()
	{
		return $this->sControllerName;
	}

	public function setActionName($sActionName)
	{
		$this->sActionName = (string) $sActionName;
	}

	public function getActionName()
	{
		return $this->sActionName;
	}

	public function setParams($aParams)
	{
		$this->aParams = (array) $aParams;
	}

	public function getParams()
	{
		return $this->aParams;
	}

	public function getParam($key, $default = false)
	{
		if (!isset($this->aParams[$key]))
		{
			return $default;
		}

		return $this->aParams[$key];
	}
}