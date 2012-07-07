<?php

class EA_Db_Statement
{
	protected $sDbStatement;

	public function __construct($sDbStatement)
	{
		$this->sDbStatement = (string) $sDbStatement;
	}

	public function __toString()
	{
		return $this->sDbStatement;
	}
}