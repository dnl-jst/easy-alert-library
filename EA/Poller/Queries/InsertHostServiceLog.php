<?php

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