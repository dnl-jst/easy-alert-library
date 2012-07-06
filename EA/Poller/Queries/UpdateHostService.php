<?php

class EA_Poller_Queries_UpdateHostService extends EA_Db_Query
{
	protected $iHostServiceId = 0;
	protected $sNewState = '';

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
				last_run = NOW()
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
}