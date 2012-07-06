<?php

class EA_Poller_Queries_InsertHostServiceLog extends EA_Db_Query
{
	protected $iHostServiceId = 0;
	protected $sResponse = '';
	protected $sType = '';

	public function getQuery()
	{
		if ($this->iHostServiceId === 0 || empty($this->sResponse) || empty($this->sType))
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			INSERT INTO
				ea_host_service_log
				(
					hs_id,
					response,
					created
				)
			VALUES
				(
					' . (int) $this->iHostServiceId . ',
					\'' . $this->escape((string) $this->sResponse) . '\',
					NOW()
				)';

		return $sQuery;
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