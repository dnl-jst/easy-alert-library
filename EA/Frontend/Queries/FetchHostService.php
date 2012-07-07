<?php

class EA_Frontend_Queries_FetchHostService extends EA_Db_Query
{
	protected $iHostServiceId = 0;

	public function getQuery()
	{
		if ($this->iHostServiceId === 0)
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			SELECT
				h.name AS host_name,
				s.name AS service_name,
				s.key_name
			FROM
				ea_host_services hs
			JOIN ea_services s ON s.service_id = hs.service_id
			JOIN ea_hosts h ON h.host_id = hs.host_id
			WHERE
				hs.hs_id = ' . (int) $this->iHostServiceId;

		return $sQuery;
	}

	public function setHostServiceId($iHostServiceId)
	{
		$this->iHostServiceId = (int) $iHostServiceId;
	}
}