<?php

require_once Mage::getModuleDir('controllers', 'Mage_Vads').DS.'StandardController.php';

class Dbm_Customer_StandardController extends Mage_Vads_StandardController
{
    public function returnAction()
    {
        $this->_currentMethod = __METHOD__;
    	$this->log('Start');

    	$req = $this->getRequest()->getParams();

    	// Load order
    	/* @var $order Mage_Sales_Model_Order */
    	$order = Mage::getModel('sales/order');
    	$order->loadByIncrementId($req['vads_order_id']);

    	// Load standard API model to analyse response
		$this->log('Loading VadsApi');
    	$api = Mage::getModel('vads/api_standard');
		$vads_resp = $api->getResponse(
			$req,
			$this->getModel()->getConfigData('ctx_mode'),
			$this->getModel()->getConfigData('key_test'),
			$this->getModel()->getConfigData('key_prod')
		);

		if(!$vads_resp->isAuthentified()) {
			// authentification failed
			$this->log($api->getIpAddress() . " tries to access our vads/standard/return page without valid signature. It may be a hacking attempt.",
					Zend_Log::WARN);
			$this->_redirect('checkout/onepage/failure', array('_store'=>$order->getStore()->getId()));
            Mage::dispatchEvent('dbm_customer_order_error', array());
			return;
		}

		$this->log('Request authenticated');

		// Load session
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getVadsStandardQuoteId(true));

        $this->log('ORDER STATUS : ' . $order->getStatus());
        $this->log('DEFAULT STATUS : ' . $this->getStatusPendingPayment());
        $this->log(print_r($req, true));
		if($order->getStatus() == $this->getStatusPendingPayment()) {
			$this->log('Order '.$order->getId().' is waiting payment');

			// Save Platform responses
			$order->getPayment()
	        	->setAmount($req['vads_amount'] / 100)
	        	->setCcTransId($req['vads_trans_id'])
	        	->setLastTransId($req['vads_trans_id'])
	        	->setCcExpMonth($req['vads_expiry_month'])
	        	->setCcExpYear($req['vads_expiry_year'])
	        	->setCcType($req['vads_card_brand'])
	        	->setCcStatus($vads_resp->code)
				->setCcStatusDescription($vads_resp->message)
	        	->setCcNumberEnc($req['vads_card_number'])

	        	->setTransactionId($req['vads_trans_id'])
	        	->setTransactionAdditionalInfo(
	        		Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
	        		array(
	        			'Message' => $vads_resp->message . '(' . $vads_resp->code . ')',
	        			'Transaction ID' => $req['vads_trans_id'],
	        			'Credit Card Number' => $req['vads_card_number'],
	        			'Expiration Date' => str_pad($req['vads_expiry_month'], 2, '0', STR_PAD_LEFT) . ' / ' . $req['vads_expiry_year'],
	        			'Payment Mean' => $req['vads_card_brand']
	        		));

			// Add history entry
			$order->addStatusHistoryComment($vads_resp->message);

			// Order waiting for payment
			if ($vads_resp->isAcceptedPayment()) {
				$this->log('Payment for order '.$order->getId().' has been confirmed by client return ! This means the check url did not work.',
					Zend_Log::WARN);

				// Save order and create invoice
				$this->registerOrder($order);

				// Display success page
				if($this->getModel()->getConfigData('ctx_mode') == 'TEST') {
					// order not paid => not validated from check url ; ctx_mode=TEST => user is webmaster
					// So log and display a warning about check url not working
					$message = $this->__("Have you properly configured the check url in your bank backoffice ?");
					$session->addError($message);
				}

				$this->log('Redirecting to success page');
				$this->_redirect('checkout/onepage/success', array('_store'=>$order->getStore()->getId()));
                Mage::dispatchEvent('dbm_customer_order_success');
			} else {
				// Client returns with a canceled/refused payment, send him back to checkout
				$this->log('Payment for order '.$order->getId().' has failed.', Zend_Log::INFO);

				// Cancel Order and refill cart
				$this->manageRefusedPayment($order);

				$this->log('Redirecting to cart page');
				$session->addWarning($this->__('Systempay checkout and order have been canceled.'));
				$this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
                Mage::dispatchEvent('dbm_customer_order_cancel');
			}
		} else {
			// Payment already registered
			$this->log('Order '.$order->getId().' has already been registered.');

			if($vads_resp->isAcceptedPayment()) {
				$this->log('Order '.$order->getId().' is reconfirmed');
				// Deactivate quote
				Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				$this->_redirect('checkout/onepage/success', array('_store'=>$order->getStore()->getId()));
                Mage::dispatchEvent('dbm_customer_order_success');
			} elseif($order->isCanceled()) {
				$this->log('Order '.$order->getId().' cancelation is reconfirmed');
				$session->addWarning($this->__('Systempay checkout and order have been canceled.'));
				$this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
                Mage::dispatchEvent('dbm_customer_order_cancel');
			} else {
				// This is an error case, the client returns with an error code but the payment already has been accepted
				$this->log('Order '.$order->getId().' has been validated but we receive a payment error code !',
						Zend_Log::ERR);
				$message = $this->__("Received error code but the order has already been accepted");
				$session->addError($message);
				$this->_redirect('checkout/onepage/failure', array('_store'=>$order->getStore()->getId()));
                Mage::dispatchEvent('dbm_customer_order_error');
			}
		}

		$this->log('End');
    }
}