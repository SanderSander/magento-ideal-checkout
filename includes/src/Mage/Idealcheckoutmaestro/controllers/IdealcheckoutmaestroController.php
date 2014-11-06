<?php

	class Mage_Idealcheckoutmaestro_IdealcheckoutmaestroController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutmaestro'; 


		// Go to Maestro
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutmaestro/Model/Idealcheckoutmaestro.php
			$oIdealcheckoutmaestroModel = Mage::getSingleton('idealcheckoutmaestro/idealcheckoutmaestro');


			// Create transaction record and get URL to /idealcheckoutmaestro/setup.php
			$sIdealcheckoutmaestroUrl = $oIdealcheckoutmaestroModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutmaestroUrl);
			exit();
		}


		// Return from Maestro
		public function returnAction() 
		{
			$oIdealcheckoutmaestroModel = Mage::getSingleton('idealcheckoutmaestro/idealcheckoutmaestro');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutmaestroModel->validatePayment($sOrderId, $sOrderCode))
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