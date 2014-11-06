<?php

	class Mage_Idealcheckoutafterpay_IdealcheckoutafterpayController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutafterpay'; 


		// Go to AfterPay
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutafterpay/Model/Idealcheckoutafterpay.php
			$oIdealcheckoutafterpayModel = Mage::getSingleton('idealcheckoutafterpay/idealcheckoutafterpay');


			// Create transaction record and get URL to /idealcheckoutafterpay/setup.php
			$sIdealcheckoutafterpayUrl = $oIdealcheckoutafterpayModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutafterpayUrl);
			exit();
		}


		// Return from AfterPay
		public function returnAction() 
		{
			$oIdealcheckoutafterpayModel = Mage::getSingleton('idealcheckoutafterpay/idealcheckoutafterpay');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutafterpayModel->validatePayment($sOrderId, $sOrderCode))
			{ 
				$this->_redirect('checkout/onepage/success', array('_secure' => true));
			}
			else 
			{
				$this->_redirect('checkout/cart');
			} 
		}
	}

?>