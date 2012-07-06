<?php

class EA_Db
{
	protected static $oInstance = null;

	protected $sHost;
	protected $sUser;
	protected $sPass;
	protected $sName;
	protected $sCharset;

	protected $rConnection = null;

	public static function getInstance()
	{
		if (self::$oInstance === null)
		{
			self::$oInstance = new EA_Db();
		}

		return self::$oInstance;
	}

	protected function __construct()
	{
		$oConfig = EA_Config::getInstance();

		$this->sHost = $oConfig->getValue('db.host');
		$this->sUser = $oConfig->getValue('db.user');
		$this->sPass = $oConfig->getValue('db.pass');
		$this->sName = $oConfig->getValue('db.name');
		$this->sCharset = $oConfig->getValue('db.charset');
	}

	public function getConnection()
	{
		if ($this->rConnection === null || !mysql_ping($this->rConnection))
		{
			$this->rConnection = mysql_connect($this->sHost, $this->sUser, $this->sPass);

			if (!$this->rConnection)
			{
				throw new EA_Db_ConnectionFailedException();
			}

			if (!mysql_select_db($this->sName, $this->rConnection))
			{
				throw new EA_Db_ConnectionFailedException();
			}

			if (!mysql_set_charset($this->sCharset, $this->rConnection))
			{
				throw new EA_Db_ConnectionFailedException();
			}
		}

		return $this->rConnection;
	}

	public function executeQuery(EA_Db_Query $oQuery)
	{
		if (!$oQuery instanceof EA_Db_Query)
		{
			throw new InvalidArgumentException();
		}

		$sQuery = $oQuery->getQuery();

		$rResult = mysql_query($sQuery, $this->getConnection());

		if ($rResult === false)
		{
			throw new EA_Db_QueryFailedException(mysql_error($this->getConnection()));
		}

		return $rResult;
	}

	public function fetchAll(EA_Db_Query $oQuery)
	{
		$rResult = $this->executeQuery($oQuery);

		$aRows = array();

		if (mysql_num_rows($rResult) > 0)
		{
			while ($aRow = mysql_fetch_assoc($rResult))
			{
				$aRows[] = $aRow;
			}
		}

		return $aRows;
	}

	public function fetchOne(EA_Db_Query $oQuery)
	{
		$aRow = $this->fetchRow($oQuery);

		return (reset($aRow) === false) ? null : reset($aRow);
	}

	public function fetchRow(EA_Db_Query $oQuery)
	{
		$rResult = $this->executeQuery($oQuery);

		$aRow = array();

		if (mysql_num_rows($rResult) > 0)
		{
			$aRow = mysql_fetch_assoc($rResult);
		}

		return $aRow;
	}
}