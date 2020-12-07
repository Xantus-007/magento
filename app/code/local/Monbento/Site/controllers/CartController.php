<?php

require_once 'Dbm' . DS . 'AjaxAddToCart' . DS . 'controllers' . DS . 'ProductController.php';
class Monbento_Site_CartController extends Dbm_AjaxAddToCart_ProductController  
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
 
                // Check if quote has no error or if error is only presence of out of stock product in cart
                $allowedErrorMessage = Mage::helper('cataloginventory')->__('Some of the products are currently out of stock.');
                $allowedErrorMessage2 = Mage::helper('sales')->__('Some item options or their combination are not currently available.');
                $messages = $cart->getQuote()->getMessages();
                if (!$cart->getQuote()->getHasError() || (
                        (!empty($messages['stock']) &&
                        $messages['stock']->getCode() === $allowedErrorMessage) ||
                        (!empty($messages['unavailable-configuration']) &&
                        $messages['unavailable-configuration']->getCode() === $allowedErrorMessage2)
                    )){
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
                    
                    if($product->getModulePersonnalisation() == 1)
                    {
                        $selections = Mage::helper('monbento_bundle')->getSelectionsByBundleOptions($params, $product);
                        $customImage = Mage::helper('monbento_bundle')->getCustomImage($selections, 320);
                        $image = $customImage['url'];
                    }
                    elseif(isset($params['options']) && count($params['options']) >= 1) 
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
                        $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
                        $childProduct = Mage::getModel('catalog/product')->load($childProduct->getId());
                        $image = Mage::helper('catalog/image')->init($childProduct, 'image');
                    }

                    $showFranco = Mage::getStoreConfig('dbm_modal/defaultmodal/showfranco');
                    $price = Mage::helper('weee')->getAmountForDisplay($product) + Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
                    
                    $modal = $this->getLayout()->createBlock('dbm_addtocart/modal')
                        ->setTemplate('catalog/product/view/modal-addtocart.phtml')
                        ->setData('product_id', $product->getId())
                        ->setData('has_options', $hasOptions)
                        ->setData('visuel', $image)
                        ->setData('name', Mage::helper('core')->escapeHtml($product->getName()))
                        ->setData('desc', $desc)
                        ->setData('qte', $baseQty)
                        ->setData('price', Mage::helper('core')->formatPrice($price))
                        ->setData('showFrancoMsg', $showFranco)
                        ->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());

                    $response['modal'] = $modal;
                    $response['minicart'] = $this->getLayout()->createBlock('checkout/cart_minicart')->setTemplate('checkout/cart/minicart.phtml')->toHtml();
                    $response['cartprice'] = Mage::helper('checkout')->formatPrice(Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal());
                    $response['status'] = 'SUCCESS';
                } else {                    
                    $response['status'] = 'ERROR';
                    $response['message'] = Mage::helper('checkout')->__('Cannot add the item to shopping cart.');
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
                $response['message'] = Mage::helper('checkout')->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }else{
            return parent::addAction();
        }
    }
    
}