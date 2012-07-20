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

class EA_Poller_Queries_FetchJobs extends EA_Db_Query
{
	protected $iInterval;

	public function getQuery()
	{
		$sQuery = '
			SELECT
				h.host_id,
				h.parent_host_id,
				h.name AS host_name,
				h.password,
				h.address,
				h.muted,
				hs.hs_id,
				hs.configuration AS hs_configuration,
				hs.last_state,
				hs.retries,
				s.name AS service_name,
				s.key_name,
				s.configuration AS s_configuration,
				IF(hs.last_state <> \'OK\', \'emergency\', \'regular\') AS job_type
			FROM
				ea_hosts h
			JOIN ea_host_services hs ON hs.host_id = h.host_id
			JOIN ea_services s ON s.service_id = hs.service_id
			WHERE
				(
						hs.last_run IS NULL
					OR DATE_ADD(hs.last_run, INTERVAL ' . (int) $this->iInterval . ' MINUTE) < NOW()
					OR	hs.last_state <> \'OK\'
				)
			AND	h.disabled = 0
			AND	hs.disabled = 0';

		return $sQuery;
	}

	public function setInterval($iInterval)
	{
		$this->iInterval = (int) $iInterval;
	}
}