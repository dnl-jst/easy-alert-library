<?php

class EA_Socket_Daemon_Request
{
	protected $sRemoteAddress;
	protected $sCommand;

	public function setRemoteAddress($sRemoteAddress)
	{
		$this->sRemoteAddress = (string) $sRemoteAddress;
	}

	public function getRemoteAddress()
	{
		return $this->sRemoteAddress;
	}

	public function setCommand($sCommand)
	{
		$this->sCommand = (string) $sCommand;
	}

	public function getCommand()
	{
		return $this->sCommand;
	}
}
