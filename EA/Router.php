<?php

class EA_Router
{
	protected $sApplicationPath;

	public function __construct($sApplicationPath)
	{
		if (is_dir($sApplicationPath))
		{
			$this->sApplicationPath = $sApplicationPath;
		}
		else
		{
			throw new EA_Router_ApplicationPathNotFoundException();
		}
	}

	public function route()
	{
		$sRequestUri = $_SERVER['REQUEST_URI'];

		$iPosQM = strpos($sRequestUri, '?');

		if ($iPosQM === false)
		{
			$sPath = $sRequestUri;
		}
		else
		{
			$sPath = substr($sRequestUri, 0, $iPosQM);
		}

		$sPath = trim($sPath, '/');
		$aPathParts = explode('/', $sPath);

		if (empty($sPath) || count($aPathParts) === 0)
		{
			$sController = 'Index';
			$sAction     = 'index';
		}
		elseif (count($aPathParts) === 1)
		{
			$sController = ucfirst(strtolower($aPathParts[0]));
			$sAction     = 'index';
		}
		else
		{
			$sController = ucfirst(strtolower($aPathParts[0]));
			$sAction     = strtolower($aPathParts[1]);
		}

		$sControllerClassName = $sController . 'Controller';
		$sControllerPath = $this->sApplicationPath . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $sControllerClassName . '.php';

		if (!is_file($sControllerPath))
		{
			throw new EA_Router_ControllerNotFoundException();
		}

		require_once ($sControllerPath);

		if (!class_exists($sControllerClassName))
		{
			throw new EA_Router_ControllerNotFoundException();
		}

		$oController = new $sControllerClassName();

		$oRequest = new EA_Controller_Request();
		$oRequest->setApplicationPath($this->sApplicationPath);
		$oRequest->setControllerName($sController);
		$oRequest->setActionName($sAction);
		$oRequest->setParams((@$_SERVER['REQUEST_METHOD'] == 'POST') ? (array) $_POST : (array) $_GET);

		# dispatch!
		$oController->setRequest($oRequest);

		$sActionName = $sAction . 'Action';

		if (!method_exists($oController, $sActionName))
		{
			throw new EA_Router_ActionNotFoundException();
		}

		$oController->$sActionName();
	}
}