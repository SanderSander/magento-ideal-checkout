<?php

	class Mage_Idealcheckoutpaysafecard_IdealcheckoutpaysafecardController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutpaysafecard'; 


		// Go to PaySafeCard
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutpaysafecard/Model/Idealcheckoutpaysafecard.php
			$oIdealcheckoutpaysafecardModel = Mage::getSingleton('idealcheckoutpaysafecard/idealcheckoutpaysafecard');


			// Create transaction record and get URL to /idealcheckoutpaysafecard/setup.php
			$sIdealcheckoutpaysafecardUrl = $oIdealcheckoutpaysafecardModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutpaysafecardUrl);
			exit();
		}


		// Return from PaySafeCard
		public function returnAction() 
		{
			$oIdealcheckoutpaysafecardModel = Mage::getSingleton('idealcheckoutpaysafecard/idealcheckoutpaysafecard');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutpaysafecardModel->validatePayment($sOrderId, $sOrderCode))
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