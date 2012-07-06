<?php

class EA_Request
{
	protected $oCheck;
	protected $oAuth;

	public function setAuth($oAuth)
	{
		if (!$oAuth instanceof EA_Auth)
		{
			throw new EA_Check_InvalidArgumentException();
		}

		$this->oAuth = $oAuth;
	}

	public function getAuth()
	{
		return $this->oAuth;
	}

	public function setCheck(EA_Check_Abstract_Request $oCheck)
	{
		$this->oCheck = $oCheck;
	}

	public function getCheck()
	{
		return $this->oCheck;
	}
}