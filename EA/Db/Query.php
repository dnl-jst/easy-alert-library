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

abstract class EA_Db_Query
{
	abstract public function getQuery();

	protected function escape($sString)
	{
		$oDb = EA_Db::getInstance();

		return mysql_real_escape_string($sString, $oDb->getConnection());
	}

	protected function getInsert($sTableName, $aData)
	{
		$aData = array_map(array($this, 'getQuoted'), $aData);

		$sQuery = 'INSERT INTO ' . $sTableName . ' (';
		$sQuery .= join(', ', array_keys($aData));
		$sQuery .= ') VALUES (';
		$sQuery .= join(', ', $aData);
		$sQuery .= ')';

		return $sQuery;
	}

	protected function getUpdate($sTableName, $aData)
	{
		$aData = array_map(array($this, 'getQuoted'), $aData);

		$sQuery = 'UPDATE ' . $sTableName . ' SET ';

		$aUpdates = array();
		foreach ($aData as $sColumnName => $sValue)
		{
			$aUpdates[] = $sColumnName . ' = ' . $sValue;
		}

		$sQuery .= join(', ', $aUpdates);

		return $sQuery;
	}

	protected function getQuoted($mValue)
	{
		if ($mValue instanceof EA_Db_Statement)
		{
			return (string) $mValue;
		}

		$oDb = EA_Db::getInstance();

		switch (gettype($mValue))
		{
			case 'string':
				return "'" . mysql_real_escape_string($mValue, $oDb->getConnection()) . "'";

			case 'boolean':
				return ($mValue) ? 1 : 0;

			default:
				if ($mValue === null)
				{
					return 'NULL';
				}
				else
				{
					return $mValue;
				}
		}
	}
}