<?php

require_once Mage::getModuleDir('controllers', 'Quadra_Cybermut') . DS . 'PaymentController.php';

class Dbm_Customer_PaymentController extends Quadra_Cybermut_PaymentController
{

    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCybermutPaymentQuoteId());
        $session->unsCybermutPaymentQuoteId();
        $session->setCanRedirect(false);
        $session->setIsMultishipping(false);

        if ($this->getQuote()->getIsMultiShipping())
            $orderIds = array();

        foreach ($this->getRealOrderIds() as $realOrderId)
        {
            $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderId);

            if (!$order->getId())
            {
                $this->norouteAction();
                return;
            }

            $order->addStatusHistoryComment(Mage::helper('cybermut')->__('Customer successfully returned from Cybermut'));
            $order->save();

            if ($this->getQuote()->getIsMultiShipping())
                $orderIds[$order->getId()] = $realOrderId;
        }

        if ($this->getQuote()->getIsMultiShipping())
        {
            Mage::getSingleton('checkout/type_multishipping')
                    ->getCheckoutSession()
                    ->setDisplaySuccess(true)
                    ->setPayboxResponseCode('success');

            Mage::getSingleton('core/session')->setOrderIds($orderIds);
            Mage::getSingleton('checkout/session')->setIsMultishipping(true);
        }

        $this->_redirect($this->_getSuccessRedirect());
        Mage::dispatchEvent('dbm_customer_order_success');
    }

    /**
     *  Failure payment page
     *
     *  @param    none
     *  @return	  void
     */
    public function errorAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $model = $this->getMethodInstance();

        $session->setIsMultishipping(false);

        foreach ($this->getRealOrderIds() as $realOrderId)
        {
            $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderId);

            if (!$order->getId())
            {
                continue;
            } else if ($order instanceof Mage_Sales_Model_Order && $order->getId())
            {
                if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PROCESSING)
                {
                    $this->_redirect('/');
                    return;
                }
                if (!$status = $model->getConfigData('order_status_payment_canceled'))
                {
                    $status = $order->getStatus();
                }

                if ($status == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold())
                {
                    $order->hold();
                } elseif ($status == Mage_Sales_Model_Order::STATE_CANCELED && $order->canCancel())
                {
                    $order->cancel();
                }
                $order->addStatusHistoryComment($this->__('Order was canceled by customer'));
                $order->save();
            }
        }

        if (!$model->getConfigData('empty_cart'))
        {
            $this->_reorder();
        }

        $this->_redirect($this->_getErrorRedirect());
        Mage::dispatchEvent('dbm_customer_order_error');
    }

}
