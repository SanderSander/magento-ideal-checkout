<?php

	class Mage_Idealcheckoutfasterpay_IdealcheckoutfasterpayController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutfasterpay'; 


		// Go to FasterPay
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutfasterpay/Model/Idealcheckoutfasterpay.php
			$oIdealcheckoutfasterpayModel = Mage::getSingleton('idealcheckoutfasterpay/idealcheckoutfasterpay');


			// Create transaction record and get URL to /idealcheckoutfasterpay/setup.php
			$sIdealcheckoutfasterpayUrl = $oIdealcheckoutfasterpayModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutfasterpayUrl);
			exit();
		}


		// Return from FasterPay
		public function returnAction() 
		{
			$oIdealcheckoutfasterpayModel = Mage::getSingleton('idealcheckoutfasterpay/idealcheckoutfasterpay');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutfasterpayModel->validatePayment($sOrderId, $sOrderCode))
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