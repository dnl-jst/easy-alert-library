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

class EA_Poller_Queries_UpdateHostService extends EA_Db_Query
{
	protected $iHostServiceId = 0;
	protected $sNewState = '';
	protected $iRetries = 0;

	public function getQuery()
	{
		if ($this->iHostServiceId === 0 || empty($this->sNewState))
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			UPDATE
				ea_host_services
			SET
				last_state = \'' . $this->escape($this->sNewState) . '\',
				last_run = NOW(),
				retries = ' . $this->iRetries . '
			WHERE
				hs_id = ' . (int) $this->iHostServiceId;

		return $sQuery;
	}

	public function setHostServiceId($iHostServiceId)
	{
		$this->iHostServiceId = (int) $iHostServiceId;
	}

	public function setNewState($sState)
	{
		if ($sState === 'OK' || $sState === 'WARNING' || $sState === 'CRITICAL')
		{
			$this->sNewState = $sState;
		}
	}

	public function setRetries($iRetries)
	{
		$this->iRetries = (int) $iRetries;
	}
}