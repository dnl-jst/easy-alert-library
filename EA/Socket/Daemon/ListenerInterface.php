<?php

interface EA_Socket_Daemon_ListenerInterface
{
	public function handleRequest(EA_Socket_Daemon_Request $oRequest);
}
