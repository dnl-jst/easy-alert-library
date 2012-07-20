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

class EA_Check_Load_Request extends EA_Check_Abstract_Request
{
	protected $fLoadWarningThreshold = 1;
	protected $fLoadCriticalThreshold = 2;

	public function doCheck()
	{
		$this->oLogger->info('checking load');

		$aLoad = sys_getloadavg();

		$oResponse = new EA_Check_Load_Response();

		if (!$aLoad)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
			$oResponse->setLoad1(0);
			$oResponse->setLoad5(0);
			$oResponse->setLoad15(0);

			return $oResponse;
		}
		elseif ($aLoad[0] > $this->fLoadWarningThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_WARNING);
		}
		elseif ($aLoad[0] > $this->fLoadCriticalThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
		}
		else
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_OK);
		}

		$oResponse->setLoad1($aLoad[0]);
		$oResponse->setLoad5($aLoad[1]);
		$oResponse->setLoad15($aLoad[2]);

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		return true;
	}

	public function setLoadWarningThreshold($fLoadWarningThreshold)
	{
		$this->fLoadWarningThreshold = (float) $fLoadWarningThreshold;
	}

	public function setLoadCriticalThreshold($fLoadCriticalThreshold)
	{
		$this>$fLoadCriticalThreshold = (float) $fLoadCriticalThreshold;
	}
}