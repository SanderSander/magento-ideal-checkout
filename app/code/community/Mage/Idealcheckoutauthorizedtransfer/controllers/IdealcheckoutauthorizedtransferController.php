<?php

	class Mage_Idealcheckoutauthorizedtransfer_IdealcheckoutauthorizedtransferController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutauthorizedtransfer'; 


		// Go to Eenmalige machtiging
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutauthorizedtransfer/Model/Idealcheckoutauthorizedtransfer.php
			$oIdealcheckoutauthorizedtransferModel = Mage::getSingleton('idealcheckoutauthorizedtransfer/idealcheckoutauthorizedtransfer');


			// Create transaction record and get URL to /idealcheckoutauthorizedtransfer/setup.php
			$sIdealcheckoutauthorizedtransferUrl = $oIdealcheckoutauthorizedtransferModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutauthorizedtransferUrl);
			exit();
		}


		// Return from Eenmalige machtiging
		public function returnAction() 
		{
			$oIdealcheckoutauthorizedtransferModel = Mage::getSingleton('idealcheckoutauthorizedtransfer/idealcheckoutauthorizedtransfer');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutauthorizedtransferModel->validatePayment($sOrderId, $sOrderCode))
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