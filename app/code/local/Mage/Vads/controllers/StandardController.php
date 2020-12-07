<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0b (révision 31978)
#									########################
#					Développé pour Magento
#						Version : 1.5.1.0
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						16/12/2011
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################

class Mage_Vads_StandardController extends Mage_Core_Controller_Front_Action
{
	/**
	 * var used in log()
	 * @var string
	 */
	var $_currentMethod;
	
	/**
	 * Return the status of the order to set while the client is on the payment gateway.
	 * It will return the status 'pending_vads' if it exists (automatically created in magento 1.3/1.4, has to be created by hand in magento 1.5)
	 * else the default "pending payment" status
	 */
	protected function getStatusPendingPayment() {
		$this->_currentMethod = __METHOD__;
		$this->log('Start');
		
		$status = null;
		
    	/* @var $stateStatusInfo Mage_Sales_Model_Order_Config */
    	$stateStatusInfo = Mage::getModel('sales/order_config');
    	if(array_key_exists('pending_vads', $stateStatusInfo->getStatuses())) {
    		$status = 'pending_vads';
    	} else { 
    		$status = $stateStatusInfo->getStateDefaultStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
    	}
    	
    	$this->log('Pending status is ' . $status);
    	$this->log('End');
    	return $status;
	}
    
    /**
     * Returns the url matching the $url argument. If $url is not already an absolute url, treats it as a magento path.
     * @param string $url
     * @param int $store_id
     */
    protected function prepareUrl($url, $store_id) {
    	$this->_currentMethod = __METHOD__;
    	$this->log('Start');
    	
    	$result = '';
    	// Preserve absolute urls
    	if(strpos($url, 'http') === 0) {
    		$result = $url;
    	} else {
    		// transform path to url
    		$result = Mage::getUrl($url, array('_secure'=>true,'_store'=>$store_id));
    	}
    	$this->log("prepareUrl : $url => $result");
    	
    	$this->log('End');
    	return $result;
    }

	/**
	 * Redirect customer to the client gateway
	 */
    public function paymentAction() {
    	$this->_currentMethod = __METHOD__;
    	$this->log('Start');

    	// Load session
    	$session = Mage::getSingleton('checkout/session');
    	$session->setVadsStandardQuoteId($session->getQuoteId());

    	// Clear redirect url : it is not useful anymore and may be called from unwanted locations (e.g. cart/add with out of stock products...)
    	$session->unsRedirectUrl();

		// Load order
    	$order_id = $session->getLastRealOrderId();
    	/* @var $order Mage_Sales_Model_Order */
    	$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($order_id);

		// Check No risk
		if(!$order->getId()) {
			$this->log("Payment attempt for an unknown order id - interrupting process.", Zend_Log::WARN);
			Mage::throwException($this->__("No order for processing found"));
			return;
		}
    	
		// Check No risk 2 : check vs. the client who had no cart...
    	if ($order->getTotalDue() == 0) {
    		$this->log("Payment attempt with no amount - redirecting to cart.");
            $this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
            return;
        }
        
    	// ------------
    	// Display form
    	// ------------
    	// Set config parameters
    	$this->log('Initialize payment parameters');
    	/* @var $standard Mage_Vads_Model_Method_Standard */
    	$api = Mage::getModel('vads/api_standard');

    	$config_fields = array('platform_url','key_test','key_prod','capture_delay','ctx_mode','site_id',
			'validation_mode', 'payment_cards','redirect_enabled','redirect_success_timeout','redirect_success_message',
    		'redirect_error_timeout','redirect_error_message','return_mode'
    	);
    	foreach($config_fields as $field) {
    		$api->set($field, $this->getModel()->getConfigData($field));
    	}
    	
    	// Set return url (build it if only path has been configured)
    	$api->set('url_return', $this->prepareUrl($this->getModel()->getConfigData('url_return'), $order->getStore()->getId()));

		$api->set('contrib', 'Magento1.5.1.0_1.0b');
		
    	$api->set('cust_email', $order->getCustomerEmail());
    	$api->set('cust_id', $order->getCustomerId());
    	$api->set('cust_name', $order->getBillingAddress()->getName());
    	$api->set('cust_address', $order->getBillingAddress()->getStreet(1) . ' ' . $order->getBillingAddress()->getStreet(2));
    	$api->set('cust_zip', $order->getBillingAddress()->getPostcode());
    	$api->set('cust_city', $order->getBillingAddress()->getCity());
    	$api->set('cust_state', $order->getBillingAddress()->getRegion());
    	$api->set('cust_country', $order->getBillingAddress()->getCountryId());
    	$api->set('cust_phone', $order->getBillingAddress()->getTelephone());
    	 
    	$address = $order->getShippingAddress();
    	if(is_object($address)) { // shipping is supported
    		$api->set('ship_to_name', $address->getName());
    		$api->set('ship_to_city', $address->getCity());
    		$api->set('ship_to_street', $address->getStreet(1));
    		$api->set('ship_to_street2', $address->getStreet(2));
    		$api->set('ship_to_state', $address->getRegion());
    		$api->set('ship_to_country', $address->getCountryId());
    		$api->set('ship_to_phone_num', $address->getTelephone());
    		$api->set('ship_to_zip', $address->getPostcode());
    	}
    	
    	// Set order_id
    	$api->set('order_id', $order_id);

    	// Set the amount to pay
		$amount = round($order->getTotalDue()*100);
		$api->set('amount', $amount);
		
		// Set currency
    	$currency = $api->vads_api->findCurrencyByAlphaCode($order->getOrderCurrency()->getCode());
		if($currency != null) {
			$api->set('currency', $currency->num);
		} else {
			$this->log("Could not find currency numeric code for currency : ".$order->getOrderCurrency()->getCode());
			Mage::throwException($this->__("Unsupported currency"));
			return;
		}

    	// Set the language code
    	$default_lang = $this->getModel()->getConfigData("language");
    	$current_lang = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
    	
		if(in_array($current_lang, $api->vads_api->getSupportedLanguages())) {
			$lang = $current_lang;
		} else {
			$lang = $default_lang;
		}
    	$api->set('language', $lang);

    	// available_languages is given as csv by magento
    	$available_languages = explode(",", $this->getModel()->getConfigData('available_languages'));
    	$available_languages = in_array("", $available_languages) ? "" : implode(";", $available_languages);
		$api->set('available_languages', $available_languages);
    	
    	// redirect to gateway
    	$this->log('Display form and javascript');
    	
    	$response  = '<html><head><title>Redirection</title></head><body>';
    	$response .= $api->getRequestHtmlForm();
    	$response .= '<script type="text/javascript">document.forms[0].style.display=\'none\'; document.forms[0].submit();</script></body></html>';
    	$this->getResponse()->setBody($response);

    	// ------------------------------------
    	// Put order on "pending payment" state
        // ------------------------------------
        $this->log("Client ".$order->getCustomerEmail().' sent to payment page for order '.$order->getId(), Zend_Log::INFO);
		$this->log('Parameters : '.$api->vads_api->getRequestFields());
        
        $stateOrder = $order->getState();
        if($stateOrder != Mage_Sales_Model_Order::STATE_CANCELED)
        {
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->getStatusPendingPayment(),
                $this->__("Pending payment, client sent to Systempay gateway"));
            $order->save();            
            $this->log('ORDER STATUS AFTER REDIRECT : ' . $order->getStatus());
        }

		$this->log('End');
    }

    /**
     * Action called after the client returns from payment gateway
     */
    public function returnAction() {
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
			return;
		}
		
		$this->log('Request authenticated');

		// Load session
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getVadsStandardQuoteId(true));
		
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
			} else {
				// Client returns with a canceled/refused payment, send him back to checkout
				$this->log('Payment for order '.$order->getId().' has failed.', Zend_Log::INFO);
				
				// Cancel Order and refill cart
				$this->manageRefusedPayment($order);
				
				$this->log('Redirecting to cart page');
				$session->addWarning($this->__('Systempay checkout and order have been canceled.'));
				$this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
			}
		} else {
			// Payment already registered
			$this->log('Order '.$order->getId().' has already been registered.');

			if($vads_resp->isAcceptedPayment()) {
				$this->log('Order '.$order->getId().' is reconfirmed');
				// Deactivate quote
				Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				$this->_redirect('checkout/onepage/success', array('_store'=>$order->getStore()->getId()));
			} elseif($order->isCanceled()) {
				$this->log('Order '.$order->getId().' cancelation is reconfirmed');
				$session->addWarning($this->__('Systempay checkout and order have been canceled.'));
				$this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
			} else {
				// This is an error case, the client returns with an error code but the payment already has been accepted
				$this->log('Order '.$order->getId().' has been validated but we receive a payment error code !',
						Zend_Log::ERR);
				$message = $this->__("Received error code but the order has already been accepted");
				$session->addError($message);
				$this->_redirect('checkout/onepage/failure', array('_store'=>$order->getStore()->getId()));
			}
		}
		
		$this->log('End');
    }


    /**
     * Action called by the payment gateway to confirm (or not) a payment
     */
    public function checkAction() {
    	$this->_currentMethod = __METHOD__;
    	$this->log('Start');
    	
    	$req = $this->getRequest()->getParams();
    	
    	if((array_key_exists('vads_payment_config', $req) && stripos($req['vads_payment_config'], 'MULTI') !== false) || stripos($req['vads_contrib'], 'multi') !== false)	{
			$this->log('Redirect to multi payment module');
			// multi-payment, load appropriate module
			$query_string = '?';
			foreach($req as $k=>$v) {
				$query_string .= $k.'='.urlencode($v).'&';
			}
			$query_string = substr($query_string, 0, -1);
			
			// We don't use magento built-in _redirect function because of troubles with overslashed urls
			$this->_redirectUrl(Mage::getUrl('vadsmulti/standard/check').$query_string);
			$this->log('End');
			return;
		}

		// Load session
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getVadsStandardQuoteId(true));
		
		// Load order
		/* @var $order Mage_Sales_Model_Order */
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($req['vads_order_id']);
		
		// Get store id from order
		$storeId = $order->getStore()->getId();
		
    	// Load standard API model to analyse response
		$this->log('Loading VadsApi');
    	$api = Mage::getModel('vads/api_standard');
		$vads_resp = $api->getResponse(
			$req,
			$this->getModel()->getConfigData('ctx_mode', $storeId),
			$this->getModel()->getConfigData('key_test', $storeId),
			$this->getModel()->getConfigData('key_prod', $storeId)
		);

		if(! $vads_resp->isAuthentified()) {
			// authentification failed
			$this->log($api->getIpAddress() . " tries to access our vads/standard/return page without valid signature. It may be a hacking attempt.",
					Zend_Log::WARN);
        	$message = $vads_resp->getOutputForGateway('auth_fail');
        	$this->getResponse()->setBody($message);
			return;
		}
		$this->log('Request authenticated');

        $this->log('ORDER STATUS : ' . $order->getStatus());
        $this->log('DEFAULT STATUS : ' . $this->getStatusPendingPayment());
        $this->log(print_r($req, true));
		if($order->getStatus() == $this->getStatusPendingPayment() ||
            $order->getStatus() == 'pending') {
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
			if($vads_resp->isAcceptedPayment()) {
				$this->log('Payment for order '.$order->getId().' has been confirmed by check url', Zend_Log::INFO);
				
				// Save order and create invoice
				$this->registerOrder($order);

				// Display check url confirmation message
				$message = $vads_resp->getOutputForGateway('payment_ok');
        		$this->getResponse()->setBody($message);
			} else {
				// Gateway refused the payment
				$this->log('Payment for order '.$order->getId().' has been invalidated by check url', Zend_Log::INFO);
				
				// Manage payment failure
				$this->manageRefusedPayment($order);
				
				// Display check url failure message 
				$message = $vads_resp->getOutputForGateway('payment_ko');
				$this->getResponse()->setBody($message);
			}
		} else {
			// Payment already registered
			if($vads_resp->isAcceptedPayment()) {
				$this->log('Order '.$order->getId().' is reconfirmed');
				$message = $vads_resp->getOutputForGateway('payment_ok_already_done');
        		$this->getResponse()->setBody($message);
			} elseif($order->isCanceled()) {
				$this->log('Order '.$order->getId().' cancelation is reconfirmed');
				$message = $vads_resp->getOutputForGateway('payment_ko');
				$this->getResponse()->setBody($message);
			} else {
				// This is an error case, the client returns with an error code but the payment already has been accepted
				$this->log('Order '.$order->getId().' has been validated but we receive a payment error code !',
						Zend_Log::ERR);
				$message = $vads_resp->getOutputForGateway('payment_ko_on_order_ok');
				$this->getResponse()->setBody($message);
			}
		}

		$this->log('End');
    }


    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function registerOrder(Mage_Sales_Model_Order $order)
    {
    	$this->_currentMethod = __METHOD__;
    	$this->log('Start');
    	
    	$this->log('Retrieving statuses configuration');
    	$newStatus = $this->getModel()->getConfigData('registered_order_status', $order->getStore()->getId());
    	/* var $stateStatusInfo Mage_Sales_Model_Order_Config */
    	$stateStatusInfo = Mage::getModel('sales/order_config');
    	$processingStatuses = $stateStatusInfo->getStateStatuses(Mage_Sales_Model_Order::STATE_PROCESSING);

        if (array_key_exists($newStatus, $processingStatuses)) {
			$this->log('Capturing payment for order '.$order->getId());
			
			if($order->canInvoice()) {
				$this->log('Creating invoice for order '.$order->getId());
				$convertor = Mage::getModel('sales/convert_order');

				$invoice = $convertor->toInvoice($order);

				foreach ($order->getAllItems() as $orderItem) {
					/*if (!$orderItem->getQtyToInvoice()) {
						continue;
					}*/
		            if (!$this->_canInvoiceItem($orderItem)) {
		                continue;
		            }

					$item = $convertor->itemToInvoiceItem($orderItem);

		            if ($orderItem->isDummy()) {
		                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
		            } else {
		                $qty = $orderItem->getQtyToInvoice();
		            }

					$item->setQty($qty);
					$invoice->addItem($item);
				}

				$invoice->collectTotals();
				$invoice->register()->capture();

				Mage::getModel('core/resource_transaction')
				  ->addObject($invoice)
				  ->addObject($invoice->getOrder())
				  ->save();
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $newStatus,
	            	Mage::helper('vads')->__('Invoice %s was created', $invoice->getIncrementId()));
			} else {
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $newStatus,
					Mage::helper('vads')->__('Order registered'));
			}
        } else {
			$this->log('Capturing payment for order '.$order->getId());
			$order->setState(Mage_Sales_Model_Order::STATE_NEW, $newStatus, Mage::helper('vads')->__('Order registered'));
        }

		$this->log('Saving confirmed order and sending email');
		$order->sendNewOrderEmail();
		$order->save();

		$this->log('End');
    }


    protected function _canInvoiceItem($item)
    {
        if ($item->getLockedDoInvoice()) {
            return false;
        }

        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if ($child->getQtyToInvoice() > 0) {
                        return true;
                    }
                }
                return false;
            } else if($item->getParentItem()) {
                $parent = $item->getParentItem();
                return $parent->getQtyToInvoice() > 0;
            }
        } else {
            return $item->getQtyToInvoice() > 0;
        }
    }


    /**
     * Cancel order 
     * @param Mage_Sales_Model_Order $order
     */
    protected function manageRefusedPayment(Mage_Sales_Model_Order $order) {
    	$this->_currentMethod = __METHOD__;
    	$this->log('Start');
    	$this->log('Canceling order '.$order->getId(), Zend_Log::INFO);
    	
    	if($this->getModel()->getConfigData('refill_cart', $order->getStore()->getId())) {
			// Re-fill the cart so that the client can reorder quicker
			$cart = Mage::getSingleton('checkout/cart');
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
	            try {
	                $cart->addOrderItem($item,$item->getQty());
	            } catch (Mage_Core_Exception $e){
	                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
	                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
	                } else {
	                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
	                }
	            } catch (Exception $e) {
	                Mage::getSingleton('checkout/session')->addException($e,
	                    Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
	                );
	            }
	        }
	        
	        // Associate cart with order customer
	        $customer = Mage::getModel('customer/customer');
	        $customer->load($order->getCustomerId());
	        $cart->getQuote()->setCustomer($customer);
	        $cart->save();
    	}
    	$order->cancel()->save();

    	/* @var $session Mage_Checkout_Model_Session */
    	$this->log('Unsetting order data in session');
    	$session = Mage::getSingleton('checkout/session');
    	$session->unsLastQuoteId()
    			->unsLastSuccessQuoteId()
    			->unsLastOrderId()
    			->unsLastRealOrderId();
    	
		$this->log('End');
    }

	/**
	 * Log function. Uses Mage::log with built-in extra data (module version, method called...)
	 * @param $message
	 * @param $level
	 */
    protected function log($message, $level=null) {
		if (!Mage::getStoreConfig('dev/log/active')) {
    		return;
    	}

    	$log  = '';
    	$log .= 'Systempay 1.0b 31978';
    	$log .= ' - '.$this->_currentMethod;
    	$log .= ' : '.$message;
		Mage::log($log, $level, 'vads.log');
    }
    
    /**
	 * Return vads payment method model.
	 */
    function getModel() {
    	return Mage::getModel('vads/standard');
    }
}
?>