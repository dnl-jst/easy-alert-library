<?php

require_once('Exception.php');
require_once('AutoLoader/PrefixNotAllowedException.php');
require_once('AutoLoader/ClassNotFoundException.php');

class EA_AutoLoader
{
	protected static $aPrefixes = array('EA');

	public static function init($aPrefixes = array(), $sPath = '')
	{
		if (count($aPrefixes) > 0)
		{
			self::$aPrefixes = array_merge(self::$aPrefixes, $aPrefixes);
		}

		if (!empty($sPath))
		{
			set_include_path(get_include_path() . PATH_SEPARATOR . $sPath);
		}

		spl_autoload_register(array('EA_AutoLoader', 'load'));
	}

	public static function load($sClassName)
	{
		if (self::classAllowed($sClassName) === false)
		{
			return;
		}

		$sClassPath = str_replace('_', '/', $sClassName) . '.php';

		require_once($sClassPath);
	}

	protected static function classAllowed($sClassName)
	{
		$sClassPrefix = substr($sClassName, 0, strpos($sClassName, '_'));

		if (!in_array($sClassPrefix, self::$aPrefixes))
		{
			return false;
		}

		return true;
	}
}
