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

class EA_Frontend_Queries_FetchHostServiceHistory extends EA_Db_Query
{
	protected $iHostServiceId;
	protected $iInterval;

	public function getQuery()
	{
		if ($this->iHostServiceId === 0 || $this->iInterval === 0)
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			SELECT
				DATE_FORMAT(hsl.created, \'%Y-%m-%d %H:%i\') AS created,
				hsl.response
			FROM
				ea_host_service_log hsl
			JOIN ea_host_services hs ON hs.hs_id = hsl.hs_id
			JOIN ea_services s ON s.service_id = hs.service_id
			WHERE
				hsl.hs_id = ' . (int) $this->iHostServiceId . '
			AND	DATE_SUB(NOW(), INTERVAL ' . (int) $this->iInterval . ' DAY) < hsl.created';

		return $sQuery;
	}

	public function setHostServiceId($iHostServiceId)
	{
		$this->iHostServiceId = (int) $iHostServiceId;
	}

	public function setInterval($iInterval)
	{
		$this->iInterval = (int) $iInterval;
	}
}