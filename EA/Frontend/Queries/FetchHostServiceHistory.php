<?php

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
				hsl.*
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