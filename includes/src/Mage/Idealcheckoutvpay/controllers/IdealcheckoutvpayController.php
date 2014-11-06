<?php

	class Mage_Idealcheckoutvpay_IdealcheckoutvpayController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutvpay'; 


		// Go to VPAY
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutvpay/Model/Idealcheckoutvpay.php
			$oIdealcheckoutvpayModel = Mage::getSingleton('idealcheckoutvpay/idealcheckoutvpay');


			// Create transaction record and get URL to /idealcheckoutvpay/setup.php
			$sIdealcheckoutvpayUrl = $oIdealcheckoutvpayModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutvpayUrl);
			exit();
		}


		// Return from VPAY
		public function returnAction() 
		{
			$oIdealcheckoutvpayModel = Mage::getSingleton('idealcheckoutvpay/idealcheckoutvpay');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutvpayModel->validatePayment($sOrderId, $sOrderCode))
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