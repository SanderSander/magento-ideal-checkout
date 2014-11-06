<?php

	// Load setup
	require_once(dirname(__FILE__) . '/includes/init.php');

	if(idealcheckout_getDebugMode())
	{
		idealcheckout_log($_GET, __FILE__, __LINE__);
		idealcheckout_log($_POST, __FILE__, __LINE__);
	}
	
	$oGateway = new Gateway();
	$oGateway->doReport();

?>