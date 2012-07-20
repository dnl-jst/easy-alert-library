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

class EA_Socket_Daemon
{
	protected $sEscapeSequence;
	protected $oLogger;

	protected $sBindAddress;
	protected $iBindPort;
	protected $iMaxClients = 10;

	protected $oListener;

	protected $aRead = array();
	protected $aClients = array();
	protected $rSocket;
	protected $iReady = 0;
	protected $bRunning = true;
	protected $bDebug = false;

	protected static $aBlacklist = array();

	public function __construct()
	{
		$this->oLogger = EA_Logger::getInstance();
		$this->sEscapeSequence = chr(255) . chr(244) . chr(255) . chr(253) . chr(6);
	}

	public function start()
	{
		$this->ready4takeoff();

		set_time_limit(0);

		$this->oLogger->info('starting socket daemon (backend ' . EA_BACKEND_VERSION . ', library ' . EA_LIBRARY_VERSION . ')');

		$this->rSocket = socket_create(AF_INET, SOCK_STREAM, 0);

		if ($this->rSocket === false)
		{
			$this->oLogger->error('unable to create socket');
			throw new EA_Socket_Daemon_UnableToCreateSocketException();
		}

		socket_set_option($this->rSocket, SOL_SOCKET, SO_REUSEADDR, 1);

		if (!socket_bind($this->rSocket, $this->sBindAddress, $this->iBindPort))
		{
			$this->oLogger->error('unable to bind address');
			throw new EA_Socket_Daemon_UnableToBindException();
		}

		socket_listen($this->rSocket, $this->iMaxClients);

		$this->loop();

		socket_close($this->rSocket);
	}

	protected function loop()
	{
		while ($this->bRunning === true)
		{
			$this->aRead[0] = $this->rSocket;

			for ($i = 0; $i < $this->iMaxClients; $i++)
			{
				if (isset($this->aClients[$i]['sock']) && $this->aClients[$i]['sock'] != null)
				{
					$this->aRead[$i + 1] = $this->aClients[$i]['sock'];
				}
			}

			$aWrite = array();
			$aExcept = array();
			$iSec = 0;

			$this->iReady = socket_select($this->aRead, $aWrite, $aExcept, $iSec);

			if ($this->iReady > 0 && in_array($this->rSocket, $this->aRead))
			{
				for ($i = 0; $i < $this->iMaxClients; $i++)
				{
					$rSocket = socket_accept($this->rSocket);

					if ($rSocket !== false)
					{
						$this->aClients[$i]['sock'] = $rSocket;

						$sRemoteAddress = '';
						socket_getpeername($this->aClients[$i]['sock'], $sRemoteAddress);

						$this->oLogger->debug('new client ' . $sRemoteAddress . ' connected');

						if ($this->isBlacklisted($sRemoteAddress))
						{
							$this->oLogger->info('blacklisted client ' . $sRemoteAddress . ' disconnected');
							socket_write($this->aClients[$i]['sock'], 'blacklisted' . chr(10));
							$this->disconnectClient($this->aClients[$i]['sock']);
							unset($this->aClients[$i]);
						}

						$this->aClients[$i]['remoteAddress'] = $sRemoteAddress;

						break;
					}
				}

				if (--$this->iReady <= 0)
				{
					continue;
				}
			}

			for ($i = 0; $i < $this->iMaxClients; $i++)
			{
				if (isset($this->aClients[$i]['sock']))
				{
					if (in_array($this->aClients[$i]['sock'], $this->aRead))
					{
						$sMessage = socket_read($this->aClients[$i]['sock'], 65536, PHP_BINARY_READ);

						if ($sMessage === null)
						{
							unset($this->aClients[$i]);
						}

						$sMessage = trim($sMessage);

						if ($sMessage == 'exit' || $sMessage === $this->sEscapeSequence || $sMessage === chr(27) || $sMessage === chr(255))
						{
							$this->disconnectClient($this->aClients[$i]['sock']);
							unset($this->aClients[$i]);
						}
						elseif ($sMessage)
						{
							$sResponse = $this->onReceive($this->aClients[$i]['remoteAddress'], $sMessage);

							socket_write($this->aClients[$i]['sock'], $sResponse);
						}

						$this->disconnectClient($this->aClients[$i]['sock']);
						unset($this->aClients[$i]);
					}
				}
			}

			sleep(5);
		}
	}

	protected function disconnectClient($rSocket)
	{
		$aReadKey = array_search($rSocket, $this->aRead);

		if ($aReadKey !== false)
		{
			unset($this->aRead[$aReadKey]);
		}

		socket_close($rSocket);
	}

	protected function ready4takeoff()
	{
		if (!$this->sBindAddress)
		{
			throw new EA_Socket_Daemon_MissingParamsException();
		}

		if (!$this->iBindPort)
		{
			throw new EA_Socket_Daemon_MissingParamsException();
		}
	}

	protected function onReceive($sRemoteAddress, $sCommand)
	{
		$sRemoteAddress = (string) $sRemoteAddress;
		$sCommand = (string) $sCommand;

		if (!$this->oListener)
		{
			return;
		}

		$oRequest = new EA_Socket_Daemon_Request();
		$oRequest->setCommand($sCommand);

		return $this->oListener->handleRequest($oRequest);
	}

	protected function isBlacklisted($sRemoteAddress)
	{
		return in_array($sRemoteAddress, self::$aBlacklist);
	}

	public static function addToBlacklist($sRemoteAddress)
	{
		self::$aBlacklist[] = $sRemoteAddress;
	}

	public static function removeFromBlacklist($sRemoteAddress)
	{
		$iKey = array_search($sRemoteAddress, self::$aBlacklist);

		if ($iKey !== false)
		{
			unset(self::$aBlacklist[$iKey]);
		}
	}

	// getters and setters

	public function setBindAddress($sBindAddress)
	{
		$this->sBindAddress = (string) $sBindAddress;
	}

	public function getBindAddress()
	{
		return $this->sBindAddress;
	}

	public function setBindPort($iBindPort)
	{
		$this->iBindPort = (int) $iBindPort;
	}

	public function getBindPort()
	{
		return $this->iBindPort;
	}

	public function setListener(EA_Socket_Daemon_ListenerInterface $oListener)
	{
		$this->oListener = $oListener;
	}

	public function setDebug($bDebug)
	{
		$this->bDebug = (bool) $bDebug;
	}
}
