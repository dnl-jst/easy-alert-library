<?php

class EA_Response
{
	protected $iErrorCode;
	protected $sErrorMessage;
	protected $sState = 'CRITICAL';

	public function setErrorMessage($sErrorMessage)
	{
		$this->sErrorMessage = (string) $sErrorMessage;
	}

	public function getErrorMessage()
	{
		return $this->sErrorMessage;
	}

	public function setErrorCode($iErrorCode)
	{
		$this->iErrorCode = (int) $iErrorCode;
	}

	public function getErrorCode()
	{
		return $this->iErrorCode;
	}

	public function setState($sState)
	{
		if ($sState == 'OK' || $sState == 'WARNING' || $sState == 'CRITICAL')
		{
			$this->sState = $sState;
		}
	}

	public function getState()
	{
		return $this->sState;
	}
}