<?php

class EA_Check_Http_Request extends EA_Check_Abstract_Request
{
	protected $sHost = '';
	protected $iPort = 0;
	protected $bSsl = null;
	protected $fResponseTimeWarningThreshold = 1;

	public function doCheck()
	{
		$sUrl = $this->getUrl();

		$this->oLogger->info('checking http against ' . $sUrl);

		$iTimeBegin = microtime(true);
		$sResponse = @file_get_contents($sUrl);
		$iDuration = microtime(true) - $iTimeBegin;

		$oResponse = new EA_Check_Response_Http();

		if ($sResponse === false)
		{
			$oResponse->setState(EA_Check_Response_Abstract::STATE_CRITICAL);
			$oResponse->setBytes(0);
			$oResponse->setResponseTime(0);

			return $oResponse;
		}
		elseif ($iDuration > $this->fResponseTimeWarningThreshold)
		{
			$oResponse->setState(EA_Check_Response_Abstract::STATE_WARNING);
		}
		else
		{
			$oResponse->setState(EA_Check_Response_Abstract::STATE_OK);
		}

		$oResponse->setBytes(strlen($sResponse));
		$oResponse->setResponseTime($iDuration);

		return $oResponse;
	}

	public function ready4Takeoff()
	{
		if (empty($this->sHost) || $this->iPort === 0 || $this->bSsl === null)
		{
			return false;
		}

		return true;
	}

	protected function getUrl()
	{
		$sUrl = ($this->bSsl) ? 'https' : 'http';
		$sUrl .= '://';
		$sUrl .= $this->sHost;
		$sUrl .= ':';
		$sUrl .= $this->iPort;
		$sUrl .= '/';

		return $sUrl;
	}

	public function setHost($sHost)
	{
		$this->sHost = (string) $sHost;
	}

	public function setPort($iPort)
	{
		$this->iPort = (int) $iPort;
	}

	public function setSsl($bSsl)
	{
		$this->bSsl = (bool) $bSsl;
	}
}