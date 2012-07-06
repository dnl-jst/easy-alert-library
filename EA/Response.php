<?php

class EA_Response
{
	protected $iErrorCode;
	protected $sErrorMessage;

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
}