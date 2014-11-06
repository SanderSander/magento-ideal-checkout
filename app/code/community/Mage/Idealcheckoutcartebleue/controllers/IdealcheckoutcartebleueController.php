<?php

	class Mage_Idealcheckoutcartebleue_IdealcheckoutcartebleueController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutcartebleue'; 


		// Go to Carte Bleue
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutcartebleue/Model/Idealcheckoutcartebleue.php
			$oIdealcheckoutcartebleueModel = Mage::getSingleton('idealcheckoutcartebleue/idealcheckoutcartebleue');


			// Create transaction record and get URL to /idealcheckoutcartebleue/setup.php
			$sIdealcheckoutcartebleueUrl = $oIdealcheckoutcartebleueModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutcartebleueUrl);
			exit();
		}


		// Return from Carte Bleue
		public function returnAction() 
		{
			$oIdealcheckoutcartebleueModel = Mage::getSingleton('idealcheckoutcartebleue/idealcheckoutcartebleue');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutcartebleueModel->validatePayment($sOrderId, $sOrderCode))
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