<?php

class EA_Request
{
	protected $oCheck;

	public function setCheck(EA_Check_Abstract $oCheck)
	{
		$this->oCheck = $oCheck;
	}

	public function getCheck()
	{
		return $this->oCheck;
	}
}