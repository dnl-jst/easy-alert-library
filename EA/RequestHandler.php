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

class EA_RequestHandler implements EA_Socket_Daemon_ListenerInterface
{
	public function handleRequest(EA_Socket_Daemon_Request $oDaemonRequest)
	{
		$logger = EA_Logger::getInstance();

		if (!$oDaemonRequest instanceof EA_Socket_Daemon_Request)
		{
			$logger->info('request not instance of EA_Socket_Daemon_Request');
			return;
		}

		$sRequest = $oDaemonRequest->getCommand();

		if (!$oRequest = @unserialize($sRequest))
		{
			$logger->info('command not unserializable');
			return;
		}

		if (!$oRequest instanceof EA_Request)
		{
			$logger->info('command not instance of EA_Request');
			return;
		}

		$oAuth = $oRequest->getAuth();

		if ($oAuth === null || !$oAuth instanceof EA_Auth || $oAuth->isAuthenticated() === false)
		{
			$logger->error('authentication failed');

			$oResponse = new EA_Response();
			$oResponse->setErrorCode(403);
			$oResponse->setErrorMessage('authentication failed');
		}
		else
		{
			$logger->debug('authentication succeeded');

			$oCheck = $oRequest->getCheck();

			if (!$oCheck->ready4Takeoff())
			{
				$logger->info('check not ready 4 takeoff');

				$oResponse = new EA_Response();
				$oResponse->setErrorCode(404);
				$oResponse->setErrorMessage('invalid arguments');
			}
			else
			{
				$oCheck->setLogger($logger);
				$logger->debug('doing check ' . get_class($oCheck));

				try
				{
					$oResponse = $oCheck->doCheck();
				}
				catch (EA_Check_Exception $e)
				{
					$oResponse = new EA_Response();
					$oResponse->setErrorCode(500);
					$oResponse->setErrorMessage('internal server error');
				}
			}
		}

		if (!$oResponse instanceof EA_Check_Abstract_Response && !$oResponse instanceof EA_Response)
		{
			$logger->error('response is neither instance of EA_Check_Response_Abstract nor EA_Response');

			$oResponse = new EA_Response();
			$oResponse->setErrorCode(500);
			$oResponse->setErrorMessage('internal server error');
		}

		return serialize($oResponse);
	}
}
