<?php

class Monbento_Site_Model_Rewrite_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment
{

    /**
     * Authorize or authorize and capture payment on gateway, if applicable
     * This method is supposed to be called only when order is placed
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_payment_place_start', array('payment' => $this));
        $order = $this->getOrder();

        $this->setAmountOrdered($order->getTotalDue());
        $this->setBaseAmountOrdered($order->getBaseTotalDue());
        $this->setShippingAmount($order->getShippingAmount());
        $this->setBaseShippingAmount($order->getBaseShippingAmount());

        $methodInstance = $this->getMethodInstance();
        $methodInstance->setStore($order->getStoreId());
        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject = new Varien_Object();

        /**
         * Do order payment validation on payment method level
         */
        $methodInstance->validate();
        $action = $methodInstance->getConfigPaymentAction();
        if ($action) {
            if ($methodInstance->isInitializeNeeded()) {
                /**
                 * For method initialization we have to use original config value for payment action
                 */
                $methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
            } else {
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                switch ($action) {
                    case Mage_Payment_Model_Method_Abstract::ACTION_ORDER:
                        $this->_order($order->getBaseTotalDue());
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                        $this->_authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
                        $this->setAmountAuthorized($order->getTotalDue());
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                        $this->setAmountAuthorized($order->getTotalDue());
                        $this->setBaseAmountAuthorized($order->getBaseTotalDue());
                        $code = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getCouponCode() ? strtoupper(Mage::getSingleton('adminhtml/session_quote')->getQuote()->getCouponCode()) : '';
                        $checkDiscountCodeSav = $code == 'SAVECHANT';
                        $discountCodeSav = Mage::getStoreConfig('monbento_config/monbento_config_sav_code/liste_codes');
                        if(strlen($discountCodeSav))
                        {
                            $discountCodeSav = explode("\n", str_replace("\r", '', $discountCodeSav));
                            $checkDiscountCodeSav = in_array($code, $discountCodeSav);
                        }
                        if (!($checkDiscountCodeSav &&
                                Mage::app()->getStore()->isAdmin())) {
                            $this->capture(null);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        $this->_createBillingAgreement();

        $orderIsNotified = null;
        if ($stateObject->getState() && $stateObject->getStatus()) {
            $orderState = $stateObject->getState();
            $orderStatus = $stateObject->getStatus();
            $orderIsNotified = $stateObject->getIsNotified();
        } else {
            $orderStatus = $methodInstance->getConfigData('order_status');
            if (!$orderStatus) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            } else {
                // check if $orderStatus has assigned a state
                $states = $order->getConfig()->getStatusStates($orderStatus);
                if (count($states) == 0) {
                    $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
                }
            }
        }
        $isCustomerNotified = (null !== $orderIsNotified) ? $orderIsNotified : $order->getCustomerNoteNotify();
        $message = $order->getCustomerNote();

        // add message if order was put into review during authorization or capture
        if ($order->getState() == Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW) {
            if ($message) {
                $order->addStatusToHistory($order->getStatus(), $message, $isCustomerNotified);
            }
        } elseif ($order->getState() && ($orderStatus !== $order->getStatus() || $message)) {
            // add message to history if order state already declared
            $order->setState($orderState, $orderStatus, $message, $isCustomerNotified);
        } elseif (($order->getState() != $orderState) || ($order->getStatus() != $orderStatus) || $message) {
            // set order state
            $order->setState($orderState, $orderStatus, $message, $isCustomerNotified);
        }

        Mage::dispatchEvent('sales_order_payment_place_end', array('payment' => $this));

        return $this;
    }

}
