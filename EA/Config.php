<?php

class EA_Config
{
	const CONFIG_PATH = 'config.ini';

	protected static $oInstance = null;

	protected $aIniValues = array();

	public static function getInstance()
	{
		if (self::$oInstance === null)
		{
			self::$oInstance = new EA_Config();
		}

		return self::$oInstance;
	}

	private function __construct()
	{
		if (!is_file(self::CONFIG_PATH))
		{
			return;
		}

		$aIniValues = parse_ini_file(self::CONFIG_PATH);

		if ($aIniValues === false)
		{
			return;
		}

		$this->aIniValues = $aIniValues;
	}

	public function getValue($key)
	{
		if (!isset($this->aIniValues[$key]))
		{
			return false;
		}

		return $this->aIniValues[$key];
	}
}