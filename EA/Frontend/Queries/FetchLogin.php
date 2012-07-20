<?php

class EA_Frontend_Queries_FetchLogin extends EA_Db_Query
{
	protected $sEmail;
	protected $sPassword;

	public function getQuery()
	{
		if (empty($this->sEmail) || empty($this->sPassword))
		{
			throw new InvalidArgumentException();
		}

		$sQuery = '
			SELECT
				*,
				\'\' AS password
			FROM
				ea_contacts
			WHERE
				email = ' . $this->getQuoted($this->sEmail) . '
			AND	password = ' . $this->getQuoted(md5($this->sPassword)) .'
			AND login = 1';

		return $sQuery;
	}

	public function setEmail($sEmail)
	{
		$this->sEmail = (string) $sEmail;
	}

	public function setPassword($sPassword)
	{
		$this->sPassword = (string) $sPassword;
	}
}