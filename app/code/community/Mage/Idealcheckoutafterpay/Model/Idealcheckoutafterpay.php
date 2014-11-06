<?php

	require_once(Mage::getBaseDir('base') . '/idealcheckout/includes/library.php');

	class Mage_Idealcheckoutafterpay_Model_Idealcheckoutafterpay extends Mage_Payment_Model_Method_Abstract
	{
		protected $_code = 'idealcheckoutafterpay';

		protected $_isGateway = false;
		protected $_canAuthorize = false;
		protected $_canCapture = true;
		protected $_canCapturePartial = false;
		protected $_canRefund = false;
		protected $_canVoid = false;
		protected $_canUseInternal = false;
		protected $_canUseCheckout = true;
		protected $_canUseForMultishipping = false;   


		// This url is called after order confirmation; It triggers IdealcheckoutafterpayController->redirectAction()
		public function getOrderPlaceRedirectUrl() 
		{
			$sUrl = Mage::getUrl('idealcheckoutafterpay/idealcheckoutafterpay/redirect', array('_secure' => true));
			return $this->fixUrl($sUrl, false);
		}
	


		// Setup idealcheckoutafterpay transaction record in database - called in: IdealcheckoutafterpayController->redirectAction()
		public function setupPayment()
		{
			$iOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			$this->_order = Mage::getModel('sales/order')->load($iOrderId);
			// $iOrderId = $oSession->getCheckout()->getLastRealOrderId();
			// $this->_order = Mage::getModel('sales/order')->loadByIncrementId($iOrderId);


			// Validate order
			if(!$this->_order->getId())
			{
				Mage::throwException('Cannot load order #' . $iOrderId);
			}


			// Validate amount
			if($this->_order->getGrandTotal() < 1.00)
			{
				// Mage::throwException(Mage::helper('idealcheckoutafterpay')->__('The total amount of order #' . $iOrderId . ' is ' . $this->_order->getGrandTotal() . ', but should be at least 1.00.'));
			}



			// Load database settings
			$aDatabaseSettings = idealcheckout_getDatabaseSettings();

			$sStoreCode = idealcheckout_getStoreCode(); // Mage::app()->getStore()->getCode();
			$sGatewayCode = 'afterpay';
			$sLanguageCode = substr(Mage::app()->getLocale()->getDefaultLocale(), 0, 2); // nl, de, en
			$sCountryCode = '';
			$sCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

			if(strcasecmp($sCurrencyCode, 'GBP') === 0)
			{
				// $sLanguageCode = 'EN';
				$sCountryCode = 'UK';
			}

			$sOrderId = $this->_order->getRealOrderId();
			$sOrderCode = idealcheckout_getRandomCode(32);
			$aOrderParams = array();
			$sTransactionId = idealcheckout_getRandomCode(32);
			$sTransactionCode = idealcheckout_getRandomCode(32);
			$fTransactionAmount = $this->_order->getGrandTotal();
			$sTransactionDescription = idealcheckout_getTranslation($sLanguageCode, 'idealcheckout', 'Webshop order #{0}', array($sOrderId));

			$sReturnUrl = Mage::getUrl('idealcheckoutafterpay/idealcheckoutafterpay/return', array('_secure' => true, 'order_id' => $sOrderId, 'order_code' => $sOrderCode));
			$sReturnUrl = $this->fixUrl($sReturnUrl, false);

			$sTransactionPaymentUrl = $sReturnUrl;
			$sTransactionSuccessUrl = $sReturnUrl;
			$sTransactionPendingUrl = $sReturnUrl;
			$sTransactionFailureUrl = $sReturnUrl;

			// Store ORDER information
			$aOrderParams['order'] = array(
				'id' => $iOrderId
			);


 


			// AfterPay Data
			$aBillingAddress = $this->_order->getBillingAddress()->getData();

			if(is_object($aBillingAddress))
			{
				$aShipmentAddress = $this->_order->getShippingAddress()->getData();
			}
			else
			{
				$aShipmentAddress = $aBillingAddress;
			}

			$aOrder = $this->_order->getData();
			$aCartItems = $this->_order->getItemsCollection()->getData();

			$aOrderParams['customer'] = array();


			// Shipment data
			$a = preg_split('/([0-9]+)/', $aShipmentAddress['street'], 2, PREG_SPLIT_DELIM_CAPTURE);
			$sStreetName = array_shift($a);
			$sStreetNumber = implode('', $a);

			$aOrderParams['customer']['shipment_company'] = $aShipmentAddress['company'];
			$aOrderParams['customer']['shipment_first_name'] = $aShipmentAddress['firstname'];
			$aOrderParams['customer']['shipment_last_name'] = $aShipmentAddress['lastname'];
			$aOrderParams['customer']['shipment_gender'] = '';
			$aOrderParams['customer']['shipment_date_of_birth'] = '';
			$aOrderParams['customer']['shipment_phone'] = $aShipmentAddress['telephone'];
			$aOrderParams['customer']['shipment_email'] = $aShipmentAddress['email'];
			$aOrderParams['customer']['shipment_street_name'] = $sStreetName;
			$aOrderParams['customer']['shipment_street_number'] = $sStreetNumber;
			$aOrderParams['customer']['shipment_zipcode'] = $aShipmentAddress['postcode'];
			$aOrderParams['customer']['shipment_city'] = $aShipmentAddress['city'];
			$aOrderParams['customer']['shipment_country_code'] = $aShipmentAddress['country_id'];
			$aOrderParams['customer']['shipment_country_name'] = ((strcasecmp($aShipmentAddress['country_id'], 'BE') === 0) ? 'Belgie' : 'Nederland');


			// Payment data
			$a = preg_split('/([0-9]+)/', $aBillingAddress['street'], 2, PREG_SPLIT_DELIM_CAPTURE);
			$sStreetName = array_shift($a);
			$sStreetNumber = implode('', $a);

			$aOrderParams['customer']['payment_company'] = $aBillingAddress['company'];
			$aOrderParams['customer']['payment_first_name'] = $aBillingAddress['firstname'];
			$aOrderParams['customer']['payment_last_name'] = $aBillingAddress['lastname'];
			$aOrderParams['customer']['payment_gender'] = '';
			$aOrderParams['customer']['payment_date_of_birth'] = '';
			$aOrderParams['customer']['payment_phone'] = $aBillingAddress['telephone'];
			$aOrderParams['customer']['payment_email'] = $aBillingAddress['email'];
			$aOrderParams['customer']['payment_street_name'] = $sStreetName;
			$aOrderParams['customer']['payment_street_number'] = $sStreetNumber;
			$aOrderParams['customer']['payment_zipcode'] = $aBillingAddress['postcode'];
			$aOrderParams['customer']['payment_city'] = $aBillingAddress['city'];
			$aOrderParams['customer']['payment_country_code'] = $aBillingAddress['country_id'];
			$aOrderParams['customer']['payment_country_name'] = ((strcasecmp($aBillingAddress['country_id'], 'BE') === 0) ? 'Belgie' : 'Nederland');


			// Products
			$aOrderParams['products'] = array();

			foreach($aCartItems as $k => $aCartItem)
			{
				if(!empty($aCartItem['description']))
				{
					$sDescription = $aCartItem['description'];
				}
				elseif(!empty($aCartItem['name']))
				{
					$sDescription = $aCartItem['name'];
				}
				else
				{
					$sDescription = $aCartItem['sku'];
				}

				$aOrderParams['products'][] = array('code' => $aCartItem['item_id'], 'description' => $sDescription, 'quantity' => $aCartItem['qty_ordered'], 'price_incl' => $aCartItem['base_price_incl_tax'], 'price_excl' => $aCartItem['base_original_price'], 'vat' => $aCartItem['tax_percent']);
			}


			// Discount?
			if(isset($aOrder['base_discount_amount']) && ($aOrder['base_discount_amount'] != 0))
			{
				$fBaseDiscountAmount = floatval($aOrder['base_discount_amount']);
				$fBaseDiscountTaxAmount = 0;
				$fBaseDiscountTax = 0;

				$aOrderParams['products'][] = array('code' => 'DISCOUNT', 'description' => 'Korting en overige kosten', 'quantity' => '1', 'price_incl' => ($fBaseDiscountAmount + $fBaseDiscountTaxAmount), 'price_excl' => $fBaseDiscountAmount, 'vat' => $fBaseDiscountTax);
			}


			// Shipping costs?
			if(isset($aOrder['base_shipping_amount']) && ($aOrder['base_shipping_amount'] > 0))
			{
				$fBaseShipmentAmount = floatval($aOrder['base_shipping_amount']);
				$fBaseShipmentTaxAmount = floatval($aOrder['base_shipping_tax_amount']);
				$fBaseShipmentTax = round(($aOrder['base_shipping_tax_amount'] / $aOrder['base_shipping_amount']) * 100);

				$aOrderParams['products'][] = array('code' => 'SHIPPING', 'description' => 'Verzendkosten', 'quantity' => '1', 'price_incl' => ($fBaseShipmentAmount + $fBaseShipmentTaxAmount), 'price_excl' => $fBaseShipmentAmount, 'vat' => $fBaseShipmentTax);
			}


			// Payment costs?
			// if(isset($aOrder['base_payment_amount']) && ($aOrder['base_payment_amount'] > 0))
			// {
			//	$fBasePaymentAmount = floatval($aOrder['base_payment_amount']);
			//	$fBasePaymentTaxAmount = 0;
			//	$fBasePaymentTax = 0;
			//	$aOrderParams['products'][] = array('code' => 'PAYMENT', 'description' => 'Betaalkosten', 'quantity' => '1', 'price_incl' => ($fBasePaymentAmount + $fBasePaymentTaxAmount), 'price_excl' => $fBasePaymentAmount, 'vat' => $fBasePaymentTax);
			// }





			// Insert data into idealcheckout-table
			$sql = "INSERT INTO `" . $aDatabaseSettings['table'] . "` SET 
`id` = NULL, 
`order_id` = '" . idealcheckout_escapeSql($sOrderId) . "', 
`order_code` = '" . idealcheckout_escapeSql($sOrderCode) . "', 
`order_params` = '" . idealcheckout_escapeSql(idealcheckout_serialize($aOrderParams)) . "', 
`store_code` = " . (empty($sStoreCode) ? "NULL" : "'" . idealcheckout_escapeSql($sStoreCode) . "'") . ", 
`gateway_code` = '" . idealcheckout_escapeSql($sGatewayCode) . "', 
`language_code` = " . (empty($sLanguageCode) ? "NULL" : "'" . idealcheckout_escapeSql($sLanguageCode) . "'") . ", 
`country_code` = " . (empty($sCountryCode) ? "NULL" : "'" . idealcheckout_escapeSql($sCountryCode) . "'") . ", 
`currency_code` = '" . idealcheckout_escapeSql($sCurrencyCode) . "', 
`transaction_id` = '" . idealcheckout_escapeSql($sTransactionId) . "', 
`transaction_code` = '" . idealcheckout_escapeSql($sTransactionCode) . "', 
`transaction_params` = NULL, 
`transaction_date` = '" . idealcheckout_escapeSql(time()) . "', 
`transaction_amount` = '" . idealcheckout_escapeSql($fTransactionAmount) . "', 
`transaction_description` = '" . idealcheckout_escapeSql($sTransactionDescription) . "', 
`transaction_status` = NULL, 
`transaction_url` = NULL, 
`transaction_payment_url` = '" . idealcheckout_escapeSql($sTransactionPaymentUrl) . "', 
`transaction_success_url` = '" . idealcheckout_escapeSql($sTransactionSuccessUrl) . "', 
`transaction_pending_url` = '" . idealcheckout_escapeSql($sTransactionPendingUrl) . "', 
`transaction_failure_url` = '" . idealcheckout_escapeSql($sTransactionFailureUrl) . "', 
`transaction_log` = NULL;";


			// Add record to transaction table
			// Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
			idealcheckout_database_query($sql);



			// Return AfterPay URL
			$sSetupUrl = Mage::getBaseUrl() . 'idealcheckout/setup.php?order_id=' . $sOrderId . '&order_code=' . $sOrderCode;
			$sSetupUrl = $this->fixUrl($sSetupUrl, true);
			return $sSetupUrl;
		}

		// Validate payment after user returns from AfterPay - called in: IdealcheckoutafterpayController->returnAction()
		public function validatePayment($sOrderId, $sOrderCode)
		{
			// Load database settings
			$aDatabaseSettings = idealcheckout_getDatabaseSettings();

			// Set default state & reply
			$sState = Mage_Sales_Model_Order::STATE_CANCELED;
			$bRestoreCart = false;

			// Find transaction record in database
			$sql = "SELECT * FROM `" . $aDatabaseSettings['table'] . "` WHERE (`order_id` = '" . idealcheckout_escapeSql($sOrderId) . "') AND (`order_code` = '" . idealcheckout_escapeSql($sOrderCode) . "') ORDER BY `id` DESC LIMIT 1;";
			$aRecord = idealcheckout_database_getRecord($sql);

			// See if record is available
			if(isset($aRecord['transaction_status'])) // if(sizeof($oRecordset))
			{
				$sTransactionStatus = $aRecord['transaction_status'];

				// Load order
				$this->_order = Mage::getModel('sales/order')->loadByIncrementId($aRecord['order_id']);

				if(in_array($sTransactionStatus, array('SUCCESS')))
				{
					$sState = Mage_Sales_Model_Order::STATE_PROCESSING;
				}
				elseif(in_array($sTransactionStatus, array('PENDING')))
				{
					$sState = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
				}
				elseif(in_array($sTransactionStatus, array('OPEN')))
				{
					header('Location: ' . $aRecord['transaction_url']);
					exit;
				}
				elseif(in_array($sTransactionStatus, array('')))
				{
					$sSetupUrl = Mage::getBaseUrl() . 'idealcheckout/setup.php?order_id=' . $sOrderId . '&order_code=' . $sOrderCode;
					$sSetupUrl = $this->fixUrl($sSetupUrl, true);

					header('Location: ' . $sSetupUrl);
					exit;
				}
				else // if(in_array($sTransactionStatus, array('EXPIRED', 'CANCELLED', 'FAILURE')))
				{
					$sState = Mage_Sales_Model_Order::STATE_CANCELED;
					$bRestoreCart = true;
				}

				// update order status
				if(strcmp($sState, $this->_order->getState()) !== 0)
				{
					if(in_array($sTransactionStatus, array('SUCCESS')))
					{
						$this->_order->getPayment()->setTransactionId($aRecord['transaction_id']);

						// Create invoice
						$oInvoice = $this->_order->prepareInvoice();
						$oInvoice->register()->capture();

						Mage::getModel('core/resource_transaction')->addObject($oInvoice)->addObject($oInvoice->getOrder())->save();

						$this->_order->setStatus('Processing');
						$this->_order->setIsInProcess(true);
						$this->_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Invoice #' . $oInvoice->getIncrementId() . ' created.');

						$this->_order->setTotalPaid($this->_order->getBaseGrandTotal())->save();

						$this->_order->sendNewOrderEmail();
						$this->_order->setEmailSent(true);
						$this->_order->save();

						return true;
					}
					elseif(in_array($sTransactionStatus, array('PENDING')))
					{
						$this->_order->setState($sState, true);
						$this->_order->save();

						return true;
					}
					else // Cancelled
					{
						$this->_order->cancel();

						$this->_order->setState($sState, true);
						$this->_order->save();

						$this->_order->setStatus('Canceled');
						$this->_order->addStatusToHistory($this->_order->getStatus(), 'Order cancelled', false);
					}
				}
				elseif(in_array($sTransactionStatus, array('SUCCESS', 'PENDING')))
				{
					return true;
				}


				// Try to restore cart
				if($bRestoreCart)
				{
					$oSession = Mage::getSingleton('checkout/session');
					$oCart = Mage::getSingleton('checkout/cart');
					$aItems = $this->_order->getItemsCollection();

					foreach($aItems as $oItem) 
					{
						try
						{
							$oCart->addOrderItem($oItem);
						}
						catch(Exception $e)
						{
						}
					}

					$oCart->save();
				}
			}

			return false;
		}

		protected function fixUrl($sUrl, $bRemoveLanguageCode = false)
		{
			if($bRemoveLanguageCode)
			{
				$sRegex = '/\/[a-z]{2,2}\//';

				while(preg_match($sRegex, $sUrl))
				{
					$sUrl = preg_replace($sRegex, '/', $sUrl);
				}
			}

			// Remove /index.php/ from URL
			while(strpos($sUrl, '/index.php/') !== false)
			{
				$sUrl = str_replace('/index.php/', '/', $sUrl);
			}

			// Remove ___SID from query string
			$sUrl = str_replace('/?___SID=U/', '/', $sUrl);

			// Replace // with /
			$sUrl = substr($sUrl, 0, 10) . str_replace('//', '/', substr($sUrl, 10));

			return $sUrl;
		}
	}

?>