<?php

require_once('EA/AutoLoader/PrefixNotAllowedException.php');
require_once('EA/AutoLoader/ClassNotFoundException.php');

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
		self::classAllowed($sClassName);

		$sClassPath = str_replace('_', '/', $sClassName) . '.php';

		if (!is_file($sClassPath))
		{
			throw new EA_AutoLoader_ClassNotFoundException();
		}

		require_once($sClassPath);
	
		if (!class_exists($sClassName))
		{
			throw new EA_AutoLoader_ClassNotFoundException();
		}
	}

	protected static classAllowed($sClassName)
	{
		$sClassPrefix = substr($sClassName, 0, strpos($sClassName, '_'));

		if (!in_array($sClassPrefixes, self::$aPrefixes))
		{
			throw new EA_AutoLoader_PrefixNotAllowedException();
		}
	}
}
