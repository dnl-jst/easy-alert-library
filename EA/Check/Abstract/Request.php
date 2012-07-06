<?php

abstract class EA_Check_Abstract_Request
{
	protected $oLogger;

	abstract public function doCheck();
	abstract public function ready4Takeoff();

	public function setLogger($oLogger)
	{
		$this->oLogger = $oLogger;
	}

	public function setConfiguration($aConfig)
	{
		foreach ($aConfig as $sKey => $sValue)
		{
			if (property_exists($this, $sKey))
			{
				$this->$sKey = $sValue;
			}
		}
	}
}