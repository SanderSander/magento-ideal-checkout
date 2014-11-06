<?php

	class Mage_Idealcheckoutclickandbuy_IdealcheckoutclickandbuyController extends Mage_Core_Controller_Front_Action
	{
		protected $_code = 'idealcheckoutclickandbuy'; 


		// Go to Click and Buy
		public function redirectAction()
		{
			// Load /app/code/community/Mage/Idealcheckoutclickandbuy/Model/Idealcheckoutclickandbuy.php
			$oIdealcheckoutclickandbuyModel = Mage::getSingleton('idealcheckoutclickandbuy/idealcheckoutclickandbuy');


			// Create transaction record and get URL to /idealcheckoutclickandbuy/setup.php
			$sIdealcheckoutclickandbuyUrl = $oIdealcheckoutclickandbuyModel->setupPayment();


			// redirect
			header('Location: ' . $sIdealcheckoutclickandbuyUrl);
			exit();
		}


		// Return from Click and Buy
		public function returnAction() 
		{
			$oIdealcheckoutclickandbuyModel = Mage::getSingleton('idealcheckoutclickandbuy/idealcheckoutclickandbuy');

			$sOrderId = $this->getRequest()->get('order_id');
			$sOrderCode = $this->getRequest()->get('order_code');


			if($oIdealcheckoutclickandbuyModel->validatePayment($sOrderId, $sOrderCode))
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