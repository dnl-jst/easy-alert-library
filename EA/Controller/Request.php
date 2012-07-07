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