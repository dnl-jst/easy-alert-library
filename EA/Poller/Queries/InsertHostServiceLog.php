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

class EA_Poller_Queries_InsertHostServiceLog extends EA_Db_Query
{
	protected $sTableName = 'ea_host_service_log';
	protected $iHostServiceId = 0;
	protected $sResponse = '';
	protected $sType = '';

	public function getQuery()
	{
		if ($this->iHostServiceId === 0 || empty($this->sResponse) || empty($this->sType))
		{
			throw new InvalidArgumentException();
		}

		$aParams = array(
			'hs_id'    => $this->iHostServiceId,
			'response' => $this->sResponse,
			'created'  => new EA_Db_Statement('NOW()')
		);

		return $this->getInsert($this->sTableName, $aParams);
	}

	public function setHostServiceId($iHostServiceId)
	{
		$this->iHostServiceId = (int) $iHostServiceId;
	}

	public function setResponse($sResponse)
	{
		$this->sResponse = (string) $sResponse;
	}

	public function setType($sType)
	{
		if ($sType === 'regular' || $sType === 'emergency')
		{
			$this->sType = $sType;
		}
	}
}