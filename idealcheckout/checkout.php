<?php

	// Allow displaying errors
	@ini_set('display_errors', 1);
	@ini_set('display_startup_errors', 1);
	@error_reporting(E_ALL | E_STRICT);


	// Load setup
	require_once(dirname(__FILE__) . '/includes/init.php');

	$sOrderId = '';
	$fOrderAmount = '';
	$sOrderDescription = '';
	$sOrderParams = '';
	$sUrlPayment = '';
	$sUrlSuccess = '';
	$sUrlPending = '';
	$sUrlFailure = '';
	$sHash = '';
	$sFormParams = '';


	$sStoreCode = md5($_SERVER['HTTP_HOST']);
	$sGatewayCode = 'ideal';
	$sCountryCode = '';
	$sLanguageCode = '';
	$sCurrencyCode = '';


	if(empty($_POST['reference']) == false)
	{
		$sOrderId = $_POST['reference'];
	}
	elseif(empty($_GET['reference']) == false)
	{
		$sOrderId = $_GET['reference'];
	}

	if(empty($_POST['amount']) == false)
	{
		$fOrderAmount = floatval(str_replace(',', '.', $_POST['amount']));
	}
	elseif(empty($_GET['amount']) == false)
	{
		$fOrderAmount = floatval(str_replace(',', '.', $_GET['amount']));
	}

	if(empty($_POST['gateway_code']) == false)
	{
		$sGatewayCode = strtolower($_POST['gateway_code']);
	}
	elseif(empty($_GET['gateway_code']) == false)
	{
		$sGatewayCode = strtolower($_GET['gateway_code']);
	}
	else
	{
		$sGatewayCode = 'ideal';
		// $sGatewayCode = 'directebanking';
		// $sGatewayCode = 'mistercash';
	}


	if(strcasecmp($sGatewayCode, 'ideal') === 0)
	{
		$fTotalAmount = ($fOrderAmount + 0.60);
	}
	elseif(strcasecmp($sGatewayCode, 'maestro') === 0)
	{
		$fTotalAmount = (($fOrderAmount * 1.015) + 0.25);
	}
	elseif(strcasecmp($sGatewayCode, 'mastercard') === 0)
	{
		$fTotalAmount = (($fOrderAmount * 1.0225) + 0.25);
	}
	elseif(strcasecmp($sGatewayCode, 'visa') === 0)
	{
		$fTotalAmount = (($fOrderAmount * 1.0225) + 0.25);	
	}

	$fPaymentCost = ($fTotalAmount - $fOrderAmount);


	if(empty($_POST['country_code']) == false)
	{
		$sCountryCode = strtoupper(substr($_POST['country_code'], 0, 2));
	}
	elseif(empty($_GET['country_code']) == false)
	{
		$sCountryCode = strtoupper(substr($_GET['country_code'], 0, 2));
	}

	if(empty($_POST['language_code']) == false)
	{
		$sLanguageCode = strtoupper(substr($_POST['language_code'], 0, 2));
	}
	elseif(empty($_GET['language_code']) == false)
	{
		$sLanguageCode = strtoupper(substr($_GET['language_code'], 0, 2));
	}

	if(empty($_POST['currency_code']) == false)
	{
		$sCurrencyCode = strtoupper(substr($_POST['currency_code'], 0, 2));
	}
	elseif(empty($_GET['currency_code']) == false)
	{
		$sCurrencyCode = strtoupper(substr($_GET['currency_code'], 0, 2));
	}

	if(empty($_POST['description']) == false)
	{
		$sOrderDescription = $_POST['description'] . 'T:' . $fPaymentCost;
	}
	elseif(empty($_GET['description']) == false)
	{
		$sOrderDescription = $_GET['description'] . 'T:' . $fPaymentCost;
	}

	if(empty($_POST['params']) == false)
	{
		$sFormParams = $_POST['params'];
	}
	elseif(empty($_GET['params']) == false)
	{
		$sFormParams = $_GET['params'];
	}
	else
	{
		foreach($_POST as $k => $v)
		{
			if(!in_array($k, array('amount', 'payment_cost', 'country_code', 'currency_code', 'description', 'gateway_code', 'language_code', 'params', 'reference', 'url_payment', 'url_success', 'url_pending', 'url_failure', 'x', 'y')))
			{
				if($sFormParams)
				{
					$sFormParams .= "\n";
				}

				$sFormParams .= ($k . ': ' . str_repeat(' ', 18 - strlen($k)) . $v);
			}
		}
	}

	if(empty($_POST['url_payment']) == false)
	{
		$sUrlPayment = $_POST['url_payment'];
	}
	elseif(empty($_GET['url_payment']) == false)
	{
		$sUrlPayment = $_GET['url_payment'];
	}

	if(empty($_POST['url_success']) == false)
	{
		$sUrlSuccess = $_POST['url_success'];
	}
	elseif(empty($_GET['url_success']) == false)
	{
		$sUrlSuccess = $_GET['url_success'];
	}

	if(empty($_POST['url_pending']) == false)
	{
		$sUrlPending = $_POST['url_pending'];
	}
	elseif(empty($_GET['url_pending']) == false)
	{
		$sUrlPending = $_GET['url_pending'];
	}

	if(empty($_POST['url_failure']) == false)
	{
		$sUrlFailure = $_POST['url_failure'];
	}
	elseif(empty($_GET['url_failure']) == false)
	{
		$sUrlFailure = $_GET['url_failure'];
	}



	$aGatewaySettings = idealcheckout_getGatewaySettings(false, $sGatewayCode);
	$aHashData = array();

	$bValidateHash = true;

	if(sizeof($_POST))
	{
		$bValidateHash = false;
	}

	if(empty($_POST['hash']) == false)
	{
		$sHash = $_POST['hash'];
		$aHashData = $_POST;
	}
	elseif(empty($_GET['hash']) == false)
	{
		$sHash = $_GET['hash'];
		$aHashData = $_GET;
	}


	// Validate hash
	if((!empty($aGatewaySettings['GATEWAY_HASH'])) && $bValidateHash)
	{
		if(empty($sHash))
		{
			idealcheckout_output('Ongeldige hash.');
		}
		elseif(strlen($sHash) != 32)
		{
			idealcheckout_output('Ongeldige hash.');
		}
		else
		{
			unset($aHashData['hash']);
			ksort($aHashData);

			$sHashData = '';

			foreach($aHashData as $k => $v)
			{
				$sHashData .= ($sHashData ? '&' : '') . $k . '=' . urlencode($v);
			}

			$sCalculatedHash = md5($aGatewaySettings['GATEWAY_HASH'] . $sHashData);

			if(strcmp($sHash, $sCalculatedHash) !== 0)
			{
				idealcheckout_output('Ongeldige hash.');
			}
		}
	}

	if(empty($sOrderId))
	{
		// Use auto_increment as id
		$sOrderId = '';
	}
	elseif(preg_match('/^[a-zA-Z0-9\-]+$/', $sOrderId) == false)
	{
		idealcheckout_output('Ongeldig betaalnummer.');
	}
	elseif(empty($sOrderDescription))
	{
		$sOrderDescription = 'Betaling ' . $sOrderId;
	}



	if(preg_match('/^[0-9]+([.][0-9]+)?$/', $fTotalAmount) == false)
	{
		idealcheckout_output('Ongeldig bedrag.');
	}
	elseif($fTotalAmount < 1.50)
	{
		idealcheckout_output('Ongeldig bedrag. Er geldt een minimum van &euro; 1,50');
	}


	$aOrderParams = array();
	$aOrderParams['data'] = $sFormParams;
	$sOrderParams = idealcheckout_serialize($aOrderParams);

	$sOrderCode = idealcheckout_getRandomCode(32);

	// Insert into #_transactions
	$sql = "INSERT INTO `" . $aIdealCheckout['database']['table'] . "` SET 
`id` = NULL, 
`order_id` = '" . idealcheckout_escapeSql($sOrderId ? $sOrderId : idealcheckout_getRandomCode(16)) . "', 
`order_code` = '" . idealcheckout_escapeSql($sOrderCode) . "', 
`order_params` = " . ($sOrderParams ? "'" . idealcheckout_escapeSql($sOrderParams) . "'" : "NULL") . ", 
`store_code` = " . ($sStoreCode ? "'" . idealcheckout_escapeSql($sStoreCode) . "'" : "NULL") . ", 
`gateway_code` = '" . idealcheckout_escapeSql($sGatewayCode) . "', 
`language_code` = " . ($sLanguageCode ? "'" . idealcheckout_escapeSql($sLanguageCode) . "'" : "NULL") . ", 
`country_code` = " . ($sCountryCode ? "'" . idealcheckout_escapeSql($sCountryCode) . "'" : "NULL") . ", 
`currency_code` = " . ($sCurrencyCode ? "'" . idealcheckout_escapeSql($sCurrencyCode) . "'" : "NULL") . ", 
`transaction_id` = '" . idealcheckout_escapeSql(idealcheckout_getRandomCode(32)) . "', 
`transaction_code` = '" . idealcheckout_escapeSql(idealcheckout_getRandomCode(32)) . "', 
`transaction_params` = " . ($sOrderParams ? "'" . idealcheckout_escapeSql($sOrderParams) . "'" : "NULL") . ", 
`transaction_date` = '" . idealcheckout_escapeSql(time()) . "', 
`transaction_amount` = '" . idealcheckout_escapeSql($fTotalAmount) . "', 
`payment_cost` = '" . idealcheckout_escapeSql($fPaymentCost) . "', 
`transaction_description` = '" . idealcheckout_escapeSql($sOrderDescription ? $sOrderDescription : idealcheckout_getRandomCode(32)) . "', 
`transaction_status` = NULL, 
`transaction_url` = NULL, 
`transaction_payment_url` = " . ($sUrlPayment ? "'" . idealcheckout_escapeSql($sUrlPayment) . "'" : "NULL") . ", 
`transaction_success_url` = " . ($sUrlSuccess ? "'" . idealcheckout_escapeSql($sUrlSuccess) . "'" : "NULL") . ", 
`transaction_pending_url` = " . ($sUrlPending ? "'" . idealcheckout_escapeSql($sUrlPending) . "'" : "NULL") . ", 
`transaction_failure_url` = " . ($sUrlFailure ? "'" . idealcheckout_escapeSql($sUrlFailure) . "'" : "NULL") . ", 
`transaction_log` = NULL;";


	// idealcheckout_database_query($sql) or die($sql . '<br><br>' . idealcheckout_database_error()); // die('#' . __LINE__);
	idealcheckout_database_query($sql) or die('#' . __LINE__);

	if(empty($sOrderId))
	{
		$sOrderId = idealcheckout_database_insert_id();
		$sql = "UPDATE `" . $aIdealCheckout['database']['table'] . "` SET 
`order_id` = '" . idealcheckout_escapeSql($sOrderId) . "'";

		if(empty($sOrderDescription))
		{
			$sql .= ",
`transaction_description` = 'Bestelling " . idealcheckout_escapeSql($sOrderId) . "'";
		}

		$sql .= "
WHERE `id` = '" . idealcheckout_escapeSql($sOrderId) . "' 
LIMIT 1;";
		idealcheckout_database_query($sql) or die('#' . __LINE__);
	}

	header('Location: setup.php?order_id=' . urlencode($sOrderId) . '&order_code=' . urlencode($sOrderCode));
	exit;

?>