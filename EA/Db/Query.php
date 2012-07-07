<?php

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