<?php

abstract class EA_Check_Abstract_Response
{
	const STATE_OK = 'OK';
	const STATE_WARNING = 'WARNING';
	const STATE_CRITICAL = 'CRITICAL';

	protected $sState;

	public function setState($sState)
	{
		$sState = (string) $sState;

		if ($sState == self::STATE_OK || $sState == self::STATE_WARNING || $sState == self::STATE_CRITICAL)
		{
			$this->sState = $sState;
		}
	}

	public function getState()
	{
		return $this->sState;
	}
}