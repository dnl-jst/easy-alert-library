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

class EA_Logger
{
	protected static $oInstance = null;

	protected $sLogFile;

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
		$oConfig = EA_Config::getInstance();
		$this->sLogFile = $oConfig->getValue('log_file');
	}

	public function info($sMessage)
	{
		$this->writeToLog('INFO - ' . $sMessage . chr(10));
	}

	public function debug($sMessage)
	{
		$this->writeToLog('DEBUG - ' . $sMessage . chr(10));
	}

	public function error($sMessage)
	{
		$this->writeToLog('ERROR - ' . $sMessage . chr(10));
	}

	protected function writeToLog($sMessage)
	{
		file_put_contents($this->sLogFile, $sMessage, FILE_APPEND);
	}
}