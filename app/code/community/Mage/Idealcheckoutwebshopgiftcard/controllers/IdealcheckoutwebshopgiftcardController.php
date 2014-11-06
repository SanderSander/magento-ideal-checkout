<?php

	class Mage_Idealcheckoutwebshopgiftcard_IdealcheckoutwebshopgiftcardController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutwebshopgiftcard'; 


		// Go to Webshop Giftcard
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutwebshopgiftcard/Model/Idealcheckoutwebshopgiftcard.php
			$oIdealcheckoutwebshopgiftcardModel = Mage::getSingleton('idealcheckoutwebshopgiftcard/idealcheckoutwebshopgiftcard');


			// Create transaction record and get URL to /idealcheckoutwebshopgiftcard/setup.php
			$sIdealcheckoutwebshopgiftcardUrl = $oIdealcheckoutwebshopgiftcardModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutwebshopgiftcardUrl);
			exit();
		}


		// Return from Webshop Giftcard
		public function returnAction() 
		{
			$oIdealcheckoutwebshopgiftcardModel = Mage::getSingleton('idealcheckoutwebshopgiftcard/idealcheckoutwebshopgiftcard');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutwebshopgiftcardModel->validatePayment($sOrderId, $sOrderCode))
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