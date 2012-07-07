<?php

/*
 * Copyright (c) 2012, Daniel Jost
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted/provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this list
 *   of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list
 *   of conditions and the following disclaimer in the documentation and/or other materials
 *   provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

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
