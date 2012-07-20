<?php

class EA_Session
{
	protected static $oInstance;

	protected $bSessionStarted = false;
	protected $aSessionVars;

	public static function getInstance()
	{
		if (self::$oInstance === null)
		{
			self::$oInstance = new EA_Session();
		}

		return self::$oInstance;
	}

	private function __construct()
	{
		if ($this->bSessionStarted === false)
		{
			session_start();
			$this->bSessionStarted = true;
		}

		$this->aSessionVars =& $_SESSION;
	}

	public function getValue($sKey, $mDefault = false)
	{
		if (!isset($this->aSessionVars[$sKey]))
		{
			return $mDefault;
		}

		return $this->aSessionVars[$sKey];
	}

	public function setValue($sKey, $mValue)
	{
		$this->aSessionVars[$sKey] = $mValue;
	}
}