<?php

class EA_Controller
{
	public $oView;
	public $oRequest;
	protected $sLayout = 'default_layout.phtml';

	public function __construct()
	{
		$this->oView = new StdClass();

		if (method_exists($this, 'init'))
		{
			$this->init();
		}
	}

	public function setRequest(EA_Controller_Request $oRequest)
	{
		$this->oRequest = $oRequest;
	}

	protected function disableLayout()
	{
		$this->sLayout = false;
	}

	public function __destruct()
	{
		$sApplicationPath = $this->oRequest->getApplicationPath();
		$sControllerName  = strtolower($this->oRequest->getControllerName());
		$sActionName      = $this->oRequest->getActionName();

		$sTplPath = $sApplicationPath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $sControllerName . DIRECTORY_SEPARATOR . $sActionName . '.phtml';

		$sContent = '';

		if (is_file($sTplPath))
		{
			ob_start();
			include($sTplPath);
			$sContent = ob_get_clean();
		}

		if ($this->sLayout)
		{
			$sLayoutPath = $sApplicationPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->sLayout;
			if (is_file($sLayoutPath))
			{
				include($sLayoutPath);
			}
		}
	}
}