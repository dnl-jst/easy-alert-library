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

class EA_Socket_Client
{
	protected $sHost;
	protected $iPort;
	protected $rSocket;
	protected $iTimeout;

	public function __construct($sHost, $iPort, $iTimeout = 5)
	{
		$this->sHost = (string) $sHost;
		$this->iPort = (int) $iPort;

		$errno = '';
		$errstr = '';
		$this->rSocket = @fsockopen($this->sHost, $this->iPort, $errno, $errstr, $iTimeout);

		if (!$this->rSocket)
		{
			throw new EA_Socket_Client_UnableToConnectException();
		}
	}

	public function __destruct()
	{
		@fclose($this->rSocket);
	}

	public function sendMessageAndGetResponse($sMessage)
	{
		fwrite($this->rSocket, $sMessage);
		$sResponse = fgets($this->rSocket);
		return $sResponse;
	}
}