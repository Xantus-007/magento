<?php

class Oye_Checkout_Model_Observer
{
    public function getDefaultCheckoutUpdate($observer)
    {
        $block = $observer->getEvent()->getBlock();

        switch ($block->getNameInLayout()) {
            case 'checkout.onepage.review':
                $this->_addReviewAdditionalHtml($block);
                return;
            default:
                return;
        }
    }

    protected function _addReviewAdditionalHtml($block)
    {
        try {
            $payment = Mage::getSingleton('checkout/type_onepage')
                ->getQuote()
                ->getPayment();
            $payment->getMethodInstance();
        } catch (Exception $e) {
            return;
        }
        $layout = $this->_prepareLayout('checkout_onepage_review');
        if ($info = $block->getChild('info')) {
            $info->setChild("items_before", $layout->getBlock("checkout.onepage.review.info.items.before"));
            $info->setChild("items_after", $layout->getBlock("checkout.onepage.review.info.items.after"));
            $info->setChild("button", $layout->getBlock("checkout.onepage.review.button"));
        }
    }

    protected function _prepareLayout($updateHandle)
    {
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate();
        $update->load($updateHandle);
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout;
    }


    public function productAfterAddToCart($observer)
    {
        if (!$observer->getEvent()->getIsAjax()) {
            if (Mage::getStoreConfig('oyecheckout/settings/bypass_cart', Mage::app()->getStore()->getId())) {
                $response = $observer->getResponse();
                $response->setRedirect(Mage::getUrl('checkout/onepage'));
                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            }
        }
    }

    public function controllerActionLayoutLoadBefore(Varien_Event_Observer $observer)
    {
        /** @var $layout Mage_Core_Model_Layout */

        if (Mage::app()->getRequest()->getRouteName() == 'oyecheckout' && Mage::app()->getRequest()->getActionName() != "success") {
            $update = $update = Mage::getSingleton('core/layout')->getUpdate();
            if (Mage::helper('oyecheckout')->isHorisontalLayout()) {
                $update->addHandle('oyecheckout_horizontal');
            } elseif (Mage::helper('oyecheckout')->isStandartLayout()) {
                //$update->addHandle('oyecheckout_onepage_index');
            }
        }
        return $this;
    }

}
