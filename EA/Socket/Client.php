<?php

class EA_Socket_Client
{
	protected $sHost;
	protected $iPort;
	protected $rSocket;
	protected $iTimeout;

	public function __construct($sHost, $iPort, $iTimeout = 5)
	{
		$this->sHost = (string) $sHost;
		$this->iPort = (int) $iPort;

		$errno = '';
		$errstr = '';
		$this->rSocket = @fsockopen($this->sHost, $this->iPort, $errno, $errstr, $iTimeout);

		if (!$this->rSocket)
		{
			throw new EA_Socket_Client_UnableToConnectException();
		}
	}

	public function __destruct()
	{
		@fclose($this->rSocket);
	}

	public function sendMessageAndGetResponse($sMessage)
	{
		fwrite($this->rSocket, $sMessage);
		$sResponse = fgets($this->rSocket);
		return $sResponse;
	}
}