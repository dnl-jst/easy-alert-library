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

class EA_Check_Tcp_Request extends EA_Check_Abstract_Request
{
	protected $sHost = '';
	protected $iPort = 0;
	protected $fResponseTimeWarningThreshold = 1;
	protected $fResponseTimeCriticalThreshold = 5;

	public function doCheck()
	{
		$this->oLogger->info('checking tcp against ' . $this->sHost . ':' . $this->iPort);

		$iTimeout = $this->fResponseTimeCriticalThreshold + 1;

		$iTimeBegin = microtime(true);

		$errno = '';
		$errstr = '';
		$rSocket = @fsockopen($this->sHost, $this->iPort, $errno, $errstr, $iTimeout);

		$iDuration = microtime(true) - $iTimeBegin;

		$oResponse = new EA_Check_Tcp_Response();

		if ($rSocket === false)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
			$oResponse->setResponseTime(0);

			return $oResponse;
		}
		elseif ($iDuration > $this->fResponseTimeCriticalThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
		}
		elseif ($iDuration > $this->fResponseTimeWarningThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_WARNING);
		}
		else
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_OK);
		}

		$oResponse->setResponseTime($iDuration);

		if ($rSocket !== false)
		{
			@fclose($rSocket);
		}

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		if (empty($this->sHost) || $this->iPort === 0)
		{
			return false;
		}

		return true;
	}

	public function setHost($sHost)
	{
		$this->sHost = (string) $sHost;
	}

	public function setPort($iPort)
	{
		$this->iPort = (int) $iPort;
	}
}