<?php

abstract class EA_Db_Query
{
	abstract public function getQuery();

	protected function escape($sString)
	{
		$oDb = EA_Db::getInstance();

		return mysql_real_escape_string($sString, $oDb->getConnection());
	}
}