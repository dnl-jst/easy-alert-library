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

class EA_Check_Procs_Request extends EA_Check_Abstract_Request
{
	protected $sGetProcessNumberCommand = 'ps ax | wc -l';

	protected $iProcsWarningThreshold = 50;
	protected $iProcsCriticalThreshold = 100;

	public function doCheck()
	{
		$this->oLogger->info('checking total processes');

		$iNumProcs = exec($this->sGetProcessNumberCommand);

		$oResponse = new EA_Check_Procs_Response();

		if (!$iNumProcs)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
			$oResponse->setNumProcs(0);

			return $oResponse;
		}
		elseif ($iNumProcs > $this->iProcsCriticalThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
		}
		elseif ($iNumProcs > $this->iProcsWarningThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_WARNING);
		}
		else
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_OK);
		}

		$oResponse->setNumProcs($iNumProcs);

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		return true;
	}

	public function setLoadWarningThreshold($iProcsWarningThreshold)
	{
		$this->iProcsWarningThreshold = (int) $iProcsWarningThreshold;
	}

	public function setLoadCriticalThreshold($iProcsCriticalThreshold)
	{
		$this->iProcsCriticalThreshold = (int) $iProcsCriticalThreshold;
	}
}