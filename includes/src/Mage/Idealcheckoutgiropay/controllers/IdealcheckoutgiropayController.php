<?php

	class Mage_Idealcheckoutgiropay_IdealcheckoutgiropayController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutgiropay'; 


		// Go to GiroPay
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutgiropay/Model/Idealcheckoutgiropay.php
			$oIdealcheckoutgiropayModel = Mage::getSingleton('idealcheckoutgiropay/idealcheckoutgiropay');


			// Create transaction record and get URL to /idealcheckoutgiropay/setup.php
			$sIdealcheckoutgiropayUrl = $oIdealcheckoutgiropayModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutgiropayUrl);
			exit();
		}


		// Return from GiroPay
		public function returnAction() 
		{
			$oIdealcheckoutgiropayModel = Mage::getSingleton('idealcheckoutgiropay/idealcheckoutgiropay');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutgiropayModel->validatePayment($sOrderId, $sOrderCode))
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