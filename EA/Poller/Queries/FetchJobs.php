<?php

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
			AND	h.disabled = 0';

		return $sQuery;
	}

	public function setInterval($iInterval)
	{
		$this->iInterval = (int) $iInterval;
	}
}