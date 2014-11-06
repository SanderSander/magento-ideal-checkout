<?php


	// Update order status when required
	function idealcheckout_update_order_status($aRecord, $sView)
	{
		// Find order state
		if(strcasecmp($aRecord['transaction_status'], 'SUCCESS') === 0)
		{
			idealcheckout_doHttpRequest($aRecord['transaction_success_url']);
		}
		elseif(strcasecmp($aRecord['transaction_status'], 'PENDING') === 0)
		{
			idealcheckout_doHttpRequest($aRecord['transaction_pending_url']);
		}
		elseif(strcasecmp($aRecord['transaction_status'], 'OPEN') === 0)
		{
			idealcheckout_doHttpRequest($aRecord['transaction_pending_url']);
		}
		elseif(strcasecmp($aRecord['transaction_status'], 'FAILURE') === 0)
		{
			idealcheckout_doHttpRequest($aRecord['transaction_failure_url']);
		}
		else
		{
			idealcheckout_doHttpRequest($aRecord['transaction_failure_url']);
		}

		idealcheckout_sendMail($aRecord);
	}


?>