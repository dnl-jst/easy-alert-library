<?php

/*
 * Copyright (c) 2012, Daniel Jost
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted/provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this list
 *   of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list
 *   of conditions and the following disclaimer in the documentation and/or other materials
 *   provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

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