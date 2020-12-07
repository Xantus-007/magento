<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Paypal') . DS . 'ExpressController.php');

class Monbento_Site_Rewrite_Paypal_ExpressController extends Mage_Paypal_ExpressController
{

    /**
     * Update shipping method (combined action for ajax and regular request)
     */
    public function saveShippingMethodAction()
    {
        try {
            $isAjax = $this->getRequest()->getParam('isAjax');
            $this->_initCheckout();
            $this->_checkout->updateShippingMethod($this->getRequest()->getParam('shipping_method'));
            Mage::dispatchEvent(
                    'checkout_controller_onepage_save_shipping_method_paypal', array(
                        'request' => $this->getRequest(),
                        'quote' => $this->_getCheckoutSession()->getQuote()));
            if ($isAjax) {
                $this->loadLayout('paypal_express_review_details');
                $this->getResponse()
                        ->setBody($this->getLayout()->getBlock('root')
                        ->setQuote($this->_getCheckoutSession()->getQuote())
                        ->toHtml());
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to update shipping method.'));
            Mage::logException($e);
        }
        if ($isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . Mage::getUrl('*/*/review') . ';</script>');
        } else {
            $this->_redirect('*/*/review');
        }
    }
    
    /**
     * PayPal session instance getter
     *
     * @return Mage_PayPal_Model_Session
     */
    private function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

}
