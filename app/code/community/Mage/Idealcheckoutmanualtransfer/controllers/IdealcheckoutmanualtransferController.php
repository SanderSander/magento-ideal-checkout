<?php

	class Mage_Idealcheckoutmanualtransfer_IdealcheckoutmanualtransferController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutmanualtransfer'; 


		// Go to Handmatig overboeken
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutmanualtransfer/Model/Idealcheckoutmanualtransfer.php
			$oIdealcheckoutmanualtransferModel = Mage::getSingleton('idealcheckoutmanualtransfer/idealcheckoutmanualtransfer');


			// Create transaction record and get URL to /idealcheckoutmanualtransfer/setup.php
			$sIdealcheckoutmanualtransferUrl = $oIdealcheckoutmanualtransferModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutmanualtransferUrl);
			exit();
		}


		// Return from Handmatig overboeken
		public function returnAction() 
		{
			$oIdealcheckoutmanualtransferModel = Mage::getSingleton('idealcheckoutmanualtransfer/idealcheckoutmanualtransfer');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutmanualtransferModel->validatePayment($sOrderId, $sOrderCode))
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