<?php

class EA_Poller_Queries_FetchHost extends EA_Db_Query
{
	protected $iHostId = 0;

	public function getQuery()
	{
		if ($this->iHostId == 0)
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			SELECT
				*
			FROM
				ea_hosts
			WHERE
				host_id = ' . (int) $this->iHostId;

		return $sQuery;
	}

	public function setHostId($iHostId)
	{
		$this->iHostId = (int) $iHostId;
	}
}