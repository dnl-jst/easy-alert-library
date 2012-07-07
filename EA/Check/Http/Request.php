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

class EA_Check_Http_Request extends EA_Check_Abstract_Request
{
	protected $sHost = '';
	protected $iPort = 0;
	protected $bSsl = null;
	protected $fResponseTimeWarningThreshold = 1;

	public function doCheck()
	{
		$sUrl = $this->getUrl();

		$this->oLogger->info('checking http against ' . $sUrl);

		$iTimeBegin = microtime(true);
		$sResponse = @file_get_contents($sUrl);
		$iDuration = microtime(true) - $iTimeBegin;

		$oResponse = new EA_Check_Http_Response();

		if ($sResponse === false)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
			$oResponse->setBytes(0);
			$oResponse->setResponseTime(0);

			return $oResponse;
		}
		elseif ($iDuration > $this->fResponseTimeWarningThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_WARNING);
		}
		else
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_OK);
		}

		$oResponse->setBytes(strlen($sResponse));
		$oResponse->setResponseTime($iDuration);

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		if (empty($this->sHost) || $this->iPort === 0 || $this->bSsl === null)
		{
			return false;
		}

		return true;
	}

	protected function getUrl()
	{
		$sUrl = ($this->bSsl) ? 'https' : 'http';
		$sUrl .= '://';
		$sUrl .= $this->sHost;
		$sUrl .= ':';
		$sUrl .= $this->iPort;
		$sUrl .= '/';

		return $sUrl;
	}

	public function setHost($sHost)
	{
		$this->sHost = (string) $sHost;
	}

	public function setPort($iPort)
	{
		$this->iPort = (int) $iPort;
	}

	public function setSsl($bSsl)
	{
		$this->bSsl = (bool) $bSsl;
	}
}