<?php

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
