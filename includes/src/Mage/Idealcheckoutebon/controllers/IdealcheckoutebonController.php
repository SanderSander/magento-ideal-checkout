<?php

	class Mage_Idealcheckoutebon_IdealcheckoutebonController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutebon'; 


		// Go to eBon
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutebon/Model/Idealcheckoutebon.php
			$oIdealcheckoutebonModel = Mage::getSingleton('idealcheckoutebon/idealcheckoutebon');


			// Create transaction record and get URL to /idealcheckoutebon/setup.php
			$sIdealcheckoutebonUrl = $oIdealcheckoutebonModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutebonUrl);
			exit();
		}


		// Return from eBon
		public function returnAction() 
		{
			$oIdealcheckoutebonModel = Mage::getSingleton('idealcheckoutebon/idealcheckoutebon');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutebonModel->validatePayment($sOrderId, $sOrderCode))
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