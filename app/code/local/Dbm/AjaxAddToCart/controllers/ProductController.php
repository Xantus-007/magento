<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Dbm_AjaxAddToCart_ProductController extends Mage_Checkout_CartController 
{
    
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        if(isset($params['isAjax']) && $params['isAjax'] == 1){
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
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }
                
                if(isset($params['modeLivraison']))
                {
                    $product->addCustomOption('mode_livraison', $params['modeLivraison']);
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
                    array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
 
                if (!$cart->getQuote()->getHasError()){
                    $this->loadLayout();
                    
                    $baseQty = (isset($params['qty'])) ? $params['qty'] : 1;
                    if(isset($params['super_group']))
                    {
                        foreach($params['super_group'] as $productId => $qty)
                        {
                            if($qty > 0) $product = Mage::getModel('catalog/product')->load($productId);
                            if($qty > 0) $baseQty = $qty;
                        }
                    }
                    
                    $image = Mage::helper('catalog/image')->init($product, 'image');
                    $desc = $product->getShortDescription();
                    $hasOptions = false;
                    $childProduct = null;
                    
                    if(isset($params['options']) && count($params['options']) >= 1) 
                    {
                        $desc = '';
                        $optionId = reset(array_keys($params['options'][reset(array_keys($params['options']))]));
                        $optionsCollection = Mage::getModel('catalog/product_option')->getProductOptionCollection($product);
                        foreach($optionsCollection as $option)
                        {
                            if($option->getId() == $optionId) {
                                $optionLabel = $option->getDefaultTitle();
                                $values = Mage::getSingleton('catalog/product_option_value')->getValuesCollection($option);
                                foreach ($values as $value)
                                {
                                    if($value->getId() == reset($params['options'][reset(array_keys($params['options']))]))
                                    {
                                        $optionValue = $value->getTitle();
                                        $tmpImage = Mage::helper('dbm_utils/image')->getOptionImage($product->getId(), $value->getTitle());
                                        if(!is_null($tmpImage)) $image = $tmpImage;
                                        break;
                                    }
                                }
                                break;
                            }
                            $desc .= '<div class="c-decli"><strong>' . $optionLabel . ' :</strong> ' . $optionValue . '</div>';
                        }
                        $hasOptions = true;
                    }
                    elseif($parentProduct = Mage::helper('dbm_utils/product')->getFirstParent($product))
                    {
                        $desc = '';
                        $attributesOfConfigurable = Mage::helper('dbm_utils/product')->getConfigurableAttributesByProduct($parentProduct);
                        foreach($attributesOfConfigurable as $attributeCode => $attributeInfos)
                        {
                            $desc .= '<div class="c-decli"><strong>' . $attributeInfos['label'] . ' :</strong> ' . Mage::helper('dbm_utils/product')->getAttributeValueForProduct($product, $attributeCode) . '</div>';
                        }
                        if($product->getImage() == 'no_selection') $image = Mage::helper('catalog/image')->init($parentProduct, 'image');
                        $hasOptions = true;
                    }
                    elseif($product->getTypeId() == 'ugiftcert')
                    {
                        $desc = '<div class="c-decli"><strong>' . $this->__('Amount') . ' :</strong> ' . Mage::helper('core')->formatPrice(Mage::helper('tax')->getPrice($product, $params['amount'])) . '</div>';
                        $desc .= '<div class="c-decli"><strong>' . $this->__('Delivery type') . ' :</strong> ' . (($params['delivery_type'] == 'virtual') ? $this->__('By Email') : $this->__('By Post')) . '</div>';
                        
                        $collection = Mage::getModel('ugiftcert/pdf_model')->getCollection();
                        foreach($collection as $template)
                        {
                            if($template->getId() == $params['pdf_template'])
                            {
                                $img = $product->getMediaGalleryImages()->getItemByColumnValue('label',$template->getTitle())->getFile();
                                $image = Mage::helper('catalog/image')->init($product, 'image', $img);
                                break;
                            }
                        }
                        $hasOptions = true;
                    }
                    elseif(isset($params['super_attribute']) && count($params['super_attribute']) >= 1) 
                    {
                        $desc = '';
                        $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
                        $childProduct = Mage::getModel('catalog/product')->load($childProduct->getId());
                        
                        if(!is_null($childProduct->getImage()) && $childProduct->getImage() !== 'no_selection') $image = Mage::helper('catalog/image')->init($childProduct, 'image');
                            
                        foreach ($params['super_attribute'] as $attributeId => $optionId)
                        {
                            $attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
                            $desc .= '<div class="c-decli">' . $attribute->getStoreLabel() . ' : ' . $childProduct->getAttributeText($attribute->getAttributeCode()) . '</div>';
                        }
                        $hasOptions = true;
                    }
                    
                    $showFranco = Mage::getStoreConfig('dbm_modal/defaultmodal/showfranco');
                    $price = Mage::helper('weee')->getAmountForDisplay($product) + Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
                    $tierPrice = $tierPriceQty = false;
                    if($prices = $product->getFormatedTierPrice())
                    {
                        if (is_array($prices))
                        {
                            foreach ($prices as $tprice)
                            {
                                if ($params['qty'] < $tprice['price_qty']) continue; // skip all quantities lower than what we currently have

                                $tierPriceQty = $tprice['price_qty']; 
                                $tierPrice = Mage::helper('core')->formatPrice($tprice['price']);
                                break;
                            }
                        }
                    }
                    
                    $modal = $this->getLayout()->createBlock('dbm_addtocart/modal')
                        ->setTemplate('catalog/product/view/modal-addtocart.phtml')
                        ->setData('product_id', $product->getId())
                        ->setData('child_product', $childProduct)
                        ->setData('has_options', $hasOptions)
                        ->setData('visuel', $image)
                        ->setData('name', Mage::helper('core')->escapeHtml($product->getName()))
                        ->setData('desc', $desc)
                        ->setData('qte', $baseQty)
                        ->setData('price', Mage::helper('core')->formatPrice($price))
                        ->setData('tier_price', $tierPrice)
                        ->setData('tier_price_qty', $tierPriceQty)
                        ->setData('showFrancoMsg', $showFranco)
                        ->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());

                    $response['modal'] = $modal;
                    $response['minicart'] = $this->getLayout()->createBlock('checkout/cart_minicart')->setTemplate('checkout/cart/minicart.phtml')->toHtml();
                    $response['cartprice'] = Mage::helper('checkout')->formatPrice(Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal());
                    $response['status'] = 'SUCCESS';
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }else{
            return parent::addAction();
        }
    }
    
    public function optionsAction(){
        $productId = $this->getRequest()->getParam('product_id');
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');
 
        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);
 
        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }
    
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }
        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $wishlist = Mage::getModel('wishlist/wishlist');
            
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }
            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }
            
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
            Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }
 
        return $wishlist;
    }
    
    public function addwishAction()
    {
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Please Login First');
        }
 
        if(empty($response)){
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to Create Wishlist');
            }else{
 
                $productId = (int) $this->getRequest()->getParam('product');
                if (!$productId) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Product Not Found');
                }else{
 
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Cannot specify product.');
                    }else{
 
                        try {
                            $requestParams = $this->getRequest()->getParams();
                            if ($session->getBeforeWishlistRequest()) {
                                $requestParams = $session->getBeforeWishlistRequest();
                                $session->unsBeforeWishlistRequest();
                            }
                            $buyRequest = new Varien_Object($requestParams);
 
                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($result);
                            }
                            $wishlist->save();
 
                            Mage::dispatchEvent(
                                'wishlist_add_product',
                            array(
                                'wishlist'  => $wishlist,
                                'product'   => $product,
                                'item'      => $result
                            )
                            );
 
                            
                            $referer = $session->getBeforeWishlistUrl();
                            if ($referer) {
                                $session->setBeforeWishlistUrl(null);
                            } else {
                                $referer = $this->_getRefererUrl();
                            }
                            $session->setAddActionReferer($referer);
                            
                            Mage::helper('wishlist')->calculate();
                            
                            $message = $this->__('%1$s has been added to your wishlist.',
                            $product->getName(), Mage::helper('core')->escapeUrl($referer));
                            
                            $response['status'] = 'SUCCESS';
                            $response['message'] = $message;
 
                            Mage::unregister('wishlist');
 
                            $this->loadLayout();
                            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                            $sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
                            $sidebar = $sidebar_block->toHtml();
                            $response['toplink'] = $toplink;
                            $response['sidebar'] = $sidebar;
                        }
                        catch (Mage_Core_Exception $e) {
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        }
                        catch (Exception $e) {
                            mage::log($e->getMessage());
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }
 
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    
    public function compareAction(){
        $response = array();
        
        $productId = (int) $this->getRequest()->getParam('product');
        
        if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
 
            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $response['status'] = 'SUCCESS';
                $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
                $this->loadLayout();
                $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
                $sidebar_block->setTemplate('ajaxwishlist/catalog/product/compare/sidebar.phtml');
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    
    public function addGiftMessageOrderAction()
    {
        $params = $this->getRequest()->getParams();
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $quote = Mage::getModel('sales/quote')->setStore(Mage::app()->getStore())->load($quoteId);
        
        if(!isset($params['allow_gift_messages_for_order']) && !empty($params['giftmessage'][$quoteId]['from']))
        {
            try {
                $quote->setGiftMessageId()->save();
            } catch (Exception $ex) {
                Mage::log('Unable to reset giftmessage_id for this quote - ERROR : '.$ex->getMessage());
            }
        }
        elseif(isset($params['allow_gift_messages_for_order']) && !empty($params['giftmessage'][$quoteId]['message']))
        {
            $giftMessage = Mage::getModel('giftmessage/message');
            if($giftMessageId = $quote->getGiftMessageId()) {
                $giftMessage->load($giftMessageId);
            }
            
            try {
                $giftMessage->setSender($params['giftmessage'][$quoteId]['from'])
                    ->setRecipient($params['giftmessage'][$quoteId]['to'])
                    ->setMessage($params['giftmessage'][$quoteId]['message'])
                    ->save();

                $quote->setGiftMessageId($giftMessage->getId())->save();
            } catch (Exception $e) {
                Mage::log('Unable to set giftmessage for this quote - ERROR : '.$e->getMessage());
            }
        }
    }
}