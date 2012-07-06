<?php

class EA_Logger
{
	protected static $oInstance = null;

	/*
	 * @return EA_Logger
	 */
	public static function getInstance()
	{
		if (self::$oInstance === null)
		{
			self::$oInstance = new EA_Logger();
		}

		return self::$oInstance;
	}

	private function __construct()
	{
		#
	}

	public function info($sMessage)
	{
		echo 'INFO - ' . $sMessage . chr(10);
	}

	public function debug($sMessage)
	{
		echo 'DEBUG - ' . $sMessage . chr(10);
	}

	public function error($sMessage)
	{
		echo 'ERROR - ' . $sMessage . chr(10);
	}
}