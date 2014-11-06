<?php

	class Mage_Idealcheckoutmastercard_IdealcheckoutmastercardController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutmastercard'; 


		// Go to Mastercard
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutmastercard/Model/Idealcheckoutmastercard.php
			$oIdealcheckoutmastercardModel = Mage::getSingleton('idealcheckoutmastercard/idealcheckoutmastercard');


			// Create transaction record and get URL to /idealcheckoutmastercard/setup.php
			$sIdealcheckoutmastercardUrl = $oIdealcheckoutmastercardModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutmastercardUrl);
			exit();
		}


		// Return from Mastercard
		public function returnAction() 
		{
			$oIdealcheckoutmastercardModel = Mage::getSingleton('idealcheckoutmastercard/idealcheckoutmastercard');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutmastercardModel->validatePayment($sOrderId, $sOrderCode))
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