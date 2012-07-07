<?php

class EA_Check_Http_Graphs
{
	protected $aGraphs = array(
		'bytes' => array(
			'title'    => 'Bytes per request',
			'function' => 'getBytes'
		),
		'response_time' => array(
			'title'    => 'Response time',
			'function' => 'getResponseTime'
		)
	);

	public function getAvailableGraphs()
	{
		return $this->aGraphs;
	}
}