<?php

/**
 * Shopping cart controller
 */
/**
 * TYhis is normally, but we have GoMage_Navigation
 */
require_once  Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
/**
 * New override
 */

/**
 * Class Oye_Ajaxcart_CartController
 */

class Oye_Checkout_CartController extends Mage_Checkout_CartController
{
    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();

        if(empty($params["isAjax"]) || $params["isAjax"] != 1) {
            return parent::addAction();
        }

        $errors = array();
        $response = array();

        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                /*
                 *  Instead of redirect, we give error message
                 */
//                $this->_goBack();
                $errors[] = Mage::helper('oye_ajaxcart')->__('No product specified!');

                if (!empty($errors)) {
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("errors" => $errors)));
                }
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();


            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse(),'is_ajax' => true)
            );
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("success" => "OK")));
            $this->getResponse()->setBody(json_encode(array("success" => "OK")));
            return;

//            if (!$this->_getSession()->getNoCartRedirect(true)) {
//                if (!$cart->getQuote()->getHasError()) {
//                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
////                    $this->_getSession()->addSuccess($message);
//                }
////                $this->_goBack();
//
//            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
//                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
                $errors[] = Mage::helper('core')->escapeHtml($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
//                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                    $errors[] = Mage::helper('core')->escapeHtml($message);
                }
            }

//            $url = $this->_getSession()->getRedirectUrl(true);
//            if ($url) {
//                $this->getResponse()->setRedirect($url);
//            } else {
//                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
//            }
        } catch (Exception $e) {
//            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            $errors[] = $this->__('Cannot add the item to shopping cart.');
            Mage::logException($e);
//            $this->_goBack();
        }
        if (!empty($errors)) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("errors" => $errors)));
        }
    }

}
