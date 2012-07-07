<?php

class EA_Frontend_Queries_FetchServiceList extends EA_Db_Query
{
	public function getQuery()
	{
		$sQuery = '
			SELECT
				hs.hs_id,
				h.name AS host_name,
				s.name AS service_name,
				hs.last_state,
				hs.last_run
			FROM
				ea_hosts h
			JOIN ea_host_services hs ON hs.host_id = h.host_id
			JOIN ea_services s ON s.service_id = hs.service_id
			ORDER BY
				h.name ASC';

		return $sQuery;
	}
}