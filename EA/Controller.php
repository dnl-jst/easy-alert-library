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

class EA_Controller
{
	public $oView;
	public $oRequest;
	protected $sLayout = 'default_layout.phtml';

	public function __construct()
	{
		$this->oView = new StdClass();

		if (method_exists($this, 'init'))
		{
			$this->init();
		}
	}

	public function setRequest(EA_Controller_Request $oRequest)
	{
		$this->oRequest = $oRequest;
	}

	protected function disableLayout()
	{
		$this->sLayout = false;
	}

	public function __destruct()
	{
		$sApplicationPath = $this->oRequest->getApplicationPath();
		$sControllerName  = strtolower($this->oRequest->getControllerName());
		$sActionName      = $this->oRequest->getActionName();

		$sTplPath = $sApplicationPath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $sControllerName . DIRECTORY_SEPARATOR . $sActionName . '.phtml';

		$sContent = '';

		if (is_file($sTplPath))
		{
			ob_start();
			include($sTplPath);
			$sContent = ob_get_clean();
		}

		if ($this->sLayout)
		{
			$sLayoutPath = $sApplicationPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->sLayout;
			if (is_file($sLayoutPath))
			{
				include($sLayoutPath);
			}
		}
	}
}