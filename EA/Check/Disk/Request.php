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

class EA_Check_Disk_Request extends EA_Check_Abstract_Request
{
	protected $sDiskPath = '';
	protected $fDiskWarningThreshold = 20;
	protected $fDiskCriticalThreshold = 10;

	public function doCheck()
	{
		$this->oLogger->info('checking free disk space');

		$fTotalDiskSpace = disk_total_space($this->sDiskPath);
		$fFreeDiskSpace = disk_free_space($this->sDiskPath);

		$fFreeDiskSpacePercentage = $fFreeDiskSpace / ($fTotalDiskSpace / 100);

		$oResponse = new EA_Check_Disk_Response();

		if ($fTotalDiskSpace === false || $fFreeDiskSpace === false)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
			$oResponse->setFreeDiskSpace(0);

			return $oResponse;
		}
		elseif ($fFreeDiskSpacePercentage < $this->fDiskCriticalThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_CRITICAL);
		}
		elseif ($fFreeDiskSpacePercentage < $this->fDiskWarningThreshold)
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_WARNING);
		}
		else
		{
			$oResponse->setState(EA_Check_Abstract_Response::STATE_OK);
		}

		$oResponse->setFreeDiskSpace($fFreeDiskSpacePercentage);

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		if (empty($this->sDiskPath))
		{
			return false;
		}

		return true;
	}

	public function setDiskWarningThreshold($fDiskWarningThreshold)
	{
		$this->fDiskWarningThreshold = (float) $fDiskWarningThreshold;
	}

	public function setDiskCriticalThreshold($fDiskCriticalThreshold)
	{
		$this->fDiskCriticalThreshold = (float) $fDiskCriticalThreshold;
	}
}