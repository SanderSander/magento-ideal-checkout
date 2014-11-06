<?php

	class Mage_Idealcheckoutvisa_IdealcheckoutvisaController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutvisa'; 


		// Go to Visa
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutvisa/Model/Idealcheckoutvisa.php
			$oIdealcheckoutvisaModel = Mage::getSingleton('idealcheckoutvisa/idealcheckoutvisa');


			// Create transaction record and get URL to /idealcheckoutvisa/setup.php
			$sIdealcheckoutvisaUrl = $oIdealcheckoutvisaModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutvisaUrl);
			exit();
		}


		// Return from Visa
		public function returnAction() 
		{
			$oIdealcheckoutvisaModel = Mage::getSingleton('idealcheckoutvisa/idealcheckoutvisa');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutvisaModel->validatePayment($sOrderId, $sOrderCode))
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