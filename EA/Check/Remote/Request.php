<?php

class EA_Check_Remote_Request extends EA_Check_Abstract_Request
{
	protected $sRemoteHost = '';
	protected $iRemotePort = 0;
	protected $oRequest = null;

	public function doCheck()
	{
		try
		{
			$oSocketClient = new EA_Socket_Client($this->sRemoteHost, $this->iRemotePort);
			$sResponse = $oSocketClient->sendMessageAndGetResponse(serialize($this->oRequest));
		}
		catch (EA_Socket_Client_UnableToConnectException $e)
		{
			throw new EA_Check_Exception();
		}

		return unserialize($sResponse);
	}

	public function ready4Takeoff()
	{
		if (empty($this->sRemoteHost) || (int) $this->iRemotePort === 0 || $this->oRequest === null)
		{
			return false;
		}

		return true;
	}

	public function setRemoteHost($sRemoteHost)
	{
		$this->sRemoteHost = (string) $sRemoteHost;
	}

	public function setRemotePort($iRemotePort)
	{
		$this->iRemotePort = (int) $iRemotePort;
	}

	public function setRequest($oRequest)
	{
		if (!$oRequest instanceof EA_Request)
		{
			throw new EA_Exception();
		}

		$this->oRequest = $oRequest;
	}
}