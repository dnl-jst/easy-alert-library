<?php

class EA_Controller_Auth
{
	protected static $oInstance = null;

	public static function getInstance()
	{
		if (self::$oInstance === null)
		{
			self::$oInstance = new EA_Controller_Auth();
		}

		return self::$oInstance;
	}

	private function __construct()
	{
		#
	}

	public function authenticate($sEmail, $sPassword)
	{
		$oSession = EA_Session::getInstance();
		$aStorage = $oSession->getValue(__CLASS__);

		$oQuery = new EA_Frontend_Queries_FetchLogin();
		$oQuery->setEmail($sEmail);
		$oQuery->setPassword($sPassword);

		$oDb = EA_Db::getInstance();
		$aContact = $oDb->fetchRow($oQuery);

		if (count($aContact))
		{
			$oIdentity = new EA_Auth_Identity();
			$oIdentity->email   = $aContact['email'];
			$oIdentity->name    = $aContact['name'];
			$oIdentity->created = $aContact['created'];

			$aStorage['identity'] = $oIdentity;

			$oSession->setValue(__CLASS__, $aStorage);
		}
	}

	public function getIdentity()
	{
		$oSession = EA_Session::getInstance();
		$aStorage = $oSession->getValue(__CLASS__);

		if (isset($aStorage['identity']) && is_object($aStorage['identity']))
		{
			if ($aStorage['identity'] instanceof EA_Auth_Identity)
			{
				return $aStorage['identity'];
			}
		}

		return false;
	}

	public function hasIdentity()
	{
		return ($this->getIdentity() === false) ? false : true;
	}
}