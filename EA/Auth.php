<?php

class EA_Auth
{
	const SALT = 'ahf$HIU84)=HFVVudwzbwe';

	protected $sPassword;

	public function isAuthenticated()
	{
		$oConfig = EA_Config::getInstance();
		$sPassword = $oConfig->getValue('password');

		if ($this->sPassword === $this->hashPassword($sPassword))
		{
			return true;
		}

		return false;
	}

	public function setPassword($sPassword)
	{
		$this->sPassword = $this->hashPassword($sPassword);
	}

	protected function hashPassword($sPassword)
	{
		return md5(self::SALT . (string) $sPassword);
	}
}