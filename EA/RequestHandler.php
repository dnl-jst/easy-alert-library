<?php

class EA_RequestHandler implements EA_Socket_Daemon_ListenerInterface
{
	public function handleRequest(EA_Socket_Daemon_Request $oRequest)
	{
		$logger = EA_Logger::getInstance();

		if (!$oRequest instanceof EA_Socket_Daemon_Request)
		{
			$logger->info('request not instance of EA_Socket_Daemon_Request');
			return;
		}

		$sCommand = $oRequest->getCommand();

		if (!$oCommand = @unserialize($sCommand))
		{
			$logger->info('command not unserializable');
			return;
		}

		if (!$oCommand instanceof EA_Request)
		{
			$logger->info('command not instance of EA_Request');
			return;
		}

		$oCheck = $oCommand->getCheck();

		if (!$oCheck->ready4Takeoff())
		{
			$logger->info('check not ready 4 takeoff');
			return;
		}

		$oCheck->setLogger($logger);

		$logger->debug('doing check ' . get_class($oCheck));

		$oResponse = $oCheck->doCheck();

		if (!$oResponse instanceof EA_Check_Abstract_Response)
		{
			$logger->error('response not instance of EA_Check_Response_Abstract');
			return;
		}

		return serialize($oResponse);
	}
}
