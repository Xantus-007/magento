<?php

/**
 * J2T-DESIGN.
 *
 * @category   J2t
 * @package    J2t_Ajaxcheckout
 * @copyright  Copyright (c) 2003-2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    GPL
 */

class J2t_Ajaxcheckout_IndexController extends /*Mage_Checkout_CartController*/ Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function cartdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)
                  ->save();
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

    public function cartAction()
    {
        if ($this->getRequest()->getParam('cart')){
            if ($this->getRequest()->getParam('cart') == "delete"){
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    try {
                        Mage::getSingleton('checkout/cart')->removeItem($id)
                          ->save();
                    } catch (Exception $e) {
                        Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
                    }
                }
            }
        }

        if ($this->getRequest()->getParam('product')) {
            $cart   = Mage::getSingleton('checkout/cart');
            $params = $this->getRequest()->getParams();
            $related = $this->getRequest()->getParam('related_product');

            $productId = (int) $this->getRequest()->getParam('product');


            if ($productId) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                
                $add = true;
                foreach($product->getCategoryIds() as $category) {
                    $cat = Mage::getModel('catalog/category')->load($category);
                    if(!Mage::helper('dbm_customer')->isCategoryAllowedForCurrentCustomer($cat)){
                        Mage::log('AJOUT PANIER RESTRICTION GOURMETS : '.$cat->getName());
                        $add = false;
                    }
                }
                
                try {

                    if(!$add){
                            $message = $this->__('%s can not be added to your cart.', $product->getName());
                            Mage::getSingleton('checkout/session')->addError($message);
                    } else {
                            if (!isset($params['qty'])) {
                                $params['qty'] = 1;
                            }

                            $cart->addProduct($product, $params);
                            if (!empty($related)) {
                                $cart->addProductsByIds(explode(',', $related));
                            }
                            $cart->save();

                            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                            Mage::getSingleton('checkout/session')->setCartInsertedItem($product->getId());

                            $img = '';
                            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product'=>$product, 'request'=>$this->getRequest()));

                            $photo_arr = explode("x",Mage::getStoreConfig('j2tajaxcheckout/default/j2t_ajax_cart_image_size', Mage::app()->getStore()->getId()));

                            // Modifications pour l'outil sur mesure
                                                if ($product->getTypeId()=='bundle' && $product->getModulePersonnalisation() == 1) {
                                                        $itemRenderer = Mage::getSingleton('checkout/cart')->getItemRenderer($product->getTypeId()); 
                                                        $bundleOptions = Mage::getModel('bundle/product_type')->getOptionsByIds(array_flip($params['bundle_option']),$product);
                                                        $optionType = array(); 
                                                        $bundleSelections = Mage::getModel('bundle/product_type')->getSelectionsByIds($params['bundle_option'],$product);
                                                        foreach ($bundleOptions as $key => $bundleOption) {
                                                            $optionTitle = explode('-',$bundleOption->getTitle());
                                                            $optionType[$params['bundle_option'][$bundleOption->getid()]] = $optionTitle[0];
                                                        }
                                                        $optionsArray = array();
                                                        foreach ($bundleSelections as $key=>$bundleSelection) {
                                                                $optionsArray[$optionType[$key]] = $bundleSelection->getEntityId() ; 
                                                        }
                                                        $img = '<img src="'.
                                                        Mage::getModel('bundle/product_type')->getCustomImage($optionsArray,$photo_arr[0],$photo_arr[1]).'" width="'.$photo_arr[0].'" height="'.$photo_arr[1].'" />';
                                                } elseif($product->getTypeId()=='ugiftcert'){
                                                    $pdf = Mage::getModel('ugiftcert/pdf_model')->load($params['pdf_template']);
                                                    $settings = Zend_Json::decode($pdf->getSettings());
                                                    $imgSettings = isset($settings['image_settings']) ? $settings['image_settings'] : array();
                                                    foreach ($imgSettings as $image) {
                                                        $img = $image['url'];
                                                        $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $img;
                                                    }
                                                    $img = '<img src="'.$img.'" width="55" />';
                                                } else {
                                                    $img = '<img src="'.Mage::helper('catalog/image')->init($product, 'image')->resize($photo_arr[0],$photo_arr[1]).'" width="'.$photo_arr[0].'" height="'.$photo_arr[1].'" />';
                                                }
                                                //
                            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                            Mage::getSingleton('checkout/session')->addSuccess('<div class="j2tajax-checkout-img">'.$img.'</div><div class="j2tajax-checkout-txt">'.$message.'</div>');
                    }      
                }
                catch (Mage_Core_Exception $e) {
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            Mage::getSingleton('checkout/session')->addError($message);
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
                }

            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

    public function addtocartAction()
    {
        $this->indexAction();
    }



    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }


}