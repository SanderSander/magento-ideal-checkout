<?php

	class Mage_Idealcheckoutpostepay_IdealcheckoutpostepayController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutpostepay'; 


		// Go to PostePay
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutpostepay/Model/Idealcheckoutpostepay.php
			$oIdealcheckoutpostepayModel = Mage::getSingleton('idealcheckoutpostepay/idealcheckoutpostepay');


			// Create transaction record and get URL to /idealcheckoutpostepay/setup.php
			$sIdealcheckoutpostepayUrl = $oIdealcheckoutpostepayModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutpostepayUrl);
			exit();
		}


		// Return from PostePay
		public function returnAction() 
		{
			$oIdealcheckoutpostepayModel = Mage::getSingleton('idealcheckoutpostepay/idealcheckoutpostepay');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutpostepayModel->validatePayment($sOrderId, $sOrderCode))
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