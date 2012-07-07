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

class EA_Poller_Handler
{
	protected $oLogger;

	public function __construct()
	{
		$this->oLogger = EA_Logger::getInstance();
	}

	public function handle()
	{
		$aJobs = $this->getJobsFromDb();

		$this->oLogger->info(count($aJobs) . ' jobs queued');

		foreach ($aJobs as $aJob)
		{
			$this->oLogger->info('checking service ' . $aJob['service_name'] . ' on host ' . $aJob['host_name']);

			$oRequest = $this->createRequest($aJob);
			$oResponse = $this->doJob($aJob, $oRequest);

			$this->handleResponse($aJob, $oResponse);
		}
	}

	protected function doJob($aJob, $oRequest)
	{
		if (!$aJob['parent_host_id'])
		{
			$oSocketClient = new EA_Socket_Client($aJob['address'], 9786, 5);
			return unserialize($oSocketClient->sendMessageAndGetResponse(serialize($oRequest)));
		}
		else
		{
			$iParentHostId = (int) $aJob['parent_host_id'];

			$oQuery = new EA_Poller_Queries_FetchHost();
			$oQuery->setHostId($iParentHostId);

			$oDb = EA_Db::getInstance();
			$aHost = $oDb->fetchRow($oQuery);

			$oCheck = new EA_Check_Remote_Request();
			$oCheck->setRemoteHost($aJob['address']);
			$oCheck->setRemotePort(9786);
			$oCheck->setRequest($oRequest);

			$oAuth = new EA_Auth();
			$oAuth->setPassword($aHost['password']);

			$oRemoteRequest = new EA_Request();
			$oRemoteRequest->setAuth($oAuth);
			$oRemoteRequest->setCheck($oCheck);

			return $this->doJob($aHost, $oRemoteRequest);
		}
	}

	protected function createRequest($aJob)
	{
		$sCheckClassName = 'EA_Check_' . $aJob['key_name'] . '_Request';

		if (!class_exists($sCheckClassName))
		{
			throw new EA_Poller_Exceptions_CheckNotFoundException();
		}

		$aConfiguration = $this->getJobConfiguration($aJob);

		$oCheck = new $sCheckClassName();
		$oCheck->setConfiguration($aConfiguration);
		$oCheck->setLogger($this->oLogger);

		if ($oCheck->ready4Takeoff() !== true)
		{
			throw new EA_Poller_Exceptions_CheckNotReadyException();
		}

		$oAuth = new EA_Auth();
		$oAuth->setPassword($aJob['password']);

		$oRequest = new EA_Request();
		$oRequest->setAuth($oAuth);
		$oRequest->setCheck($oCheck);

		return $oRequest;
	}

	protected function getJobConfiguration($aJob)
	{
		$aServiceConfig = @unserialize($aJob['s_configuration']);

		if (!$aServiceConfig)
		{
			$aServiceConfig = array();
		}

		$aHostServiceConfig = @unserialize($aJob['hs_configuration']);

		if (!$aHostServiceConfig)
		{
			$aHostServiceConfig = array();
		}

		return array_merge($aServiceConfig, $aHostServiceConfig);
	}

	protected function handleResponse($aJob, $oResponse)
	{
		$sResponse = serialize($oResponse);

		$oQuery = new EA_Poller_Queries_InsertHostServiceLog();
		$oQuery->setHostServiceId($aJob['hs_id']);
		$oQuery->setResponse($sResponse);
		$oQuery->setType($aJob['job_type']);

		$oDb = EA_Db::getInstance();
		$oDb->executeQuery($oQuery);

		if ($oResponse->getState() !== 'OK' && $oResponse->getState() === $aJob['last_state'])
		{
			$aJob['retries']++;
		}
		else
		{
			$aJob['retries'] = 1;
		}

		$this->updateHostServiceState($aJob['hs_id'], $oResponse->getState(), $aJob['retries']);

		$this->triggerNotifications($aJob, $oResponse);
	}

	protected function triggerNotifications($aJob, $oResponse)
	{
		$sLastState = $aJob['last_state'];
		$sCurrentState = $oResponse->getState();

		if ($sLastState === 'OK' && $sCurrentState !== 'OK')
		{
			$this->oLogger->info('service ' . $aJob['service_name'] . ' on host ' . $aJob['host_name'] . ' is ***' . $sCurrentState . '***');
		}
		elseif ($sLastState !== 'OK' && $sCurrentState === 'OK')
		{
			$this->oLogger->info('service ' . $aJob['service_name'] . ' on host ' . $aJob['host_name'] . ' is ***OK*** again');
		}
		elseif ($sLastState !== $sCurrentState && $sCurrentState !== 'OK')
		{
			$this->oLogger->info('service ' . $aJob['service_name'] . ' on host ' . $aJob['host_name'] . ' changed to ***' . $sCurrentState . '***');
		}
		elseif ($sLastState === $sCurrentState && $sCurrentState !== 'OK')
		{
			$this->oLogger->info('service ' . $aJob['service_name'] . ' on host ' . $aJob['host_name'] . ' is still ***' . $sCurrentState . '***');
		}
	}

	protected function getContacts($aJob, $oResponse)
	{

	}

	protected function updateHostServiceState($iHostServiceId, $sNewState, $iRetries)
	{
		$oQuery = new EA_Poller_Queries_UpdateHostService();
		$oQuery->setHostServiceId($iHostServiceId);
		$oQuery->setNewState($sNewState);
		$oQuery->setRetries($iRetries);

		$oDb = EA_Db::getInstance();
		$oDb->executeQuery($oQuery);
	}

	protected function getJobsFromDb()
	{
		$oQuery = new EA_Poller_Queries_FetchJobs();
		$oQuery->setInterval(1);

		$oDb = EA_Db::getInstance();

		try
		{
			$aJobs = $oDb->fetchAll($oQuery);
		}
		catch (EA_Db_QueryFailedException $e)
		{
			return array();
		}

		return $aJobs;
	}
}