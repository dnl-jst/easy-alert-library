<?php

class EA_Check_Http_Response extends EA_Check_Abstract_Response
{
	protected $iBytes;
	protected $fResponseTime;

	public function setBytes($iBytes)
	{
		$this->iBytes = (int) $iBytes;
	}

	public function getBytes()
	{
		return $this->iBytes;
	}

	public function setResponseTime($fResponseTime)
	{
		$this->fResponseTime = (float) $fResponseTime;
	}

	public function getResponseTime()
	{
		return $this->fResponseTime;
	}
}