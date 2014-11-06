<?php

	global $aIdealCheckout;

	// Load database settings
	require_once(dirname(__FILE__) . '/includes/init.php');



	$aQueries = array();

	// Add idealcheckout table
	$aQueries[] = "CREATE TABLE IF NOT EXISTS `" . $aIdealCheckout['database']['table'] . "` (
`id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
`order_id` VARCHAR(64) DEFAULT NULL, 
`order_code` VARCHAR(64) DEFAULT NULL, 
`order_params` TEXT DEFAULT NULL, 
`store_code` VARCHAR(64) DEFAULT NULL, 
`gateway_code` VARCHAR(64) DEFAULT NULL, 
`language_code` VARCHAR(2) DEFAULT NULL, 
`country_code` VARCHAR(2) DEFAULT NULL, 
`currency_code` VARCHAR(3) DEFAULT NULL, 
`transaction_id` VARCHAR(64) DEFAULT NULL, 
`transaction_code` VARCHAR(64) DEFAULT NULL, 
`transaction_params` TEXT DEFAULT NULL, 
`transaction_date` INT(11) UNSIGNED DEFAULT NULL, 
`transaction_amount` DECIMAL(10, 2) UNSIGNED DEFAULT NULL, 
`transaction_description` VARCHAR(100) DEFAULT NULL, 
`transaction_status` VARCHAR(16) DEFAULT NULL, 
`transaction_url` VARCHAR(255) DEFAULT NULL, 
`transaction_payment_url` VARCHAR(255) DEFAULT NULL, 
`transaction_success_url` VARCHAR(255) DEFAULT NULL, 
`transaction_pending_url` VARCHAR(255) DEFAULT NULL, 
`transaction_failure_url` VARCHAR(255) DEFAULT NULL, 
`transaction_log` TEXT DEFAULT NULL, 
PRIMARY KEY (`id`));";

	$query_html = '';
	
	for($i = 0; $i < sizeof($aQueries); $i++)
	{
		if(idealcheckout_database_query($aQueries[$i]))
		{
			// Query success
		}
		else
		{
			$query_html .= '<b>Query:</b> ' . $aQueries[$i] . '<br><b>Error:</b> ' . idealcheckout_database_error() . '<br><br><br>';
		}
	}


	// Validate files & directories
	$sBasePath = dirname(__FILE__);

	$aPaths = array();

	// Gateway files
	$aPaths[] = array('path' => $sBasePath . '/temp', 'write' => true);
	$aPaths[] = array('path' => $sBasePath . '/.htaccess', 'write' => false);

	$files_html = '';

	for($i = 0; $i < sizeof($aPaths); $i++)
	{
		if(is_file($aPaths[$i]['path']))
		{
			if($aPaths[$i]['write'] && !is_writable($aPaths[$i]['path']))
			{
				$files_html .= 'File <b>' . $aPaths[$i]['path'] . '</b> not writable.<br>';
			}
		}
		elseif(is_dir($aPaths[$i]['path']))
		{
			if($aPaths[$i]['write'] && !is_writable($aPaths[$i]['path']))
			{
				$files_html .= 'Directory <b>' . $aPaths[$i]['path'] . '</b> not writable.<br>';
			}
		}
		else
		{
			$files_html .= 'File <b>' . $aPaths[$i]['path'] . '</b> does not exist.<br>';
		}
	}



	$sFirewallCheck = @idealcheckout_doHttpRequest('https://www.ideal-checkout.nl/ping.php', array('url' => idealcheckout_getRootUrl(1), 'software' => 'magento'), true);

	echo '
<h1>INSTALL LOG</h1>
<p style="color: red;">Please remove this file (FTP: /idealcheckout/install.php) after installation!</p>

<p>&nbsp;</p>

<h3>Queries:</h3>
<code>' . ($query_html ? $query_html : 'No warnings found') . '</code>

<p>&nbsp;</p>

<h3>Files &amp; Folders:</h3>
<code>' . ($files_html ? $files_html : 'No warnings found') . '</code>

<p>&nbsp;</p>

<h3>Server checks:</h3>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left" valign="top" width="150">PHP Version</td>
		<td align="left" valign="top">' . PHP_VERSION . '</td>
	</tr>
	<tr>
		<td align="left" valign="top">OPENSSL Library</td>
		<td align="left" valign="top">' . (function_exists('openssl_sign') && defined('OPENSSL_VERSION_TEXT') ? 'Installed &nbsp; <i>(Version: ' . OPENSSL_VERSION_TEXT . ')</i>' : 'Not installed') . '</td>
	</tr>
	<tr>
		<td align="left" valign="top">FSOCK Library</td>
		<td align="left" valign="top">' . (function_exists('fsockopen') ? 'Installed' : 'Not installed') . '</td>
	</tr>
	<tr>
		<td align="left" valign="top">CURL Library</td>
		<td align="left" valign="top">' . (function_exists('curl_init') ? 'Installed' : 'Not installed') . '</td>
	</tr>
	<tr>
		<td align="left" valign="top">Firewall</td>
		<td align="left" valign="top">' . (empty($sFirewallCheck) ? 'Firewall blocks cURL/FSock' : 'OK') . '</td>
	</tr>
</table>';

?>