<?php

class Dbm_Catalog_Helper_Api extends Mage_Core_Helper_Abstract
{
    const MAX_FUNCTIONAL_ATTRIBUTES = 11;
    
    public function getImageAttributes()
    {
        return array(
            'image',
            'small_image',
            'thumbnail',
            'image_bundle_1',
            'image_bundle_2',
            'image_bundle_3',
            'bundle_button'
        );
    }
    
    public function getStockAttributes()
    {
        return array(
            'qty',
            'is_in_stock',
            'manage_stock',
            'use_config_manage_stock',
            'min_qty',
            'use_config_min_qty',
            'min_sale_qty',
            'use_config_min_sale_qty',
            'max_sale_qty',
            'use_config_max_sale_qty',
            'is_qty_decimal',
            'backorders',
            'use_config_backorders',
            'notify_stock_qty',
            'use_config_notify_stock_qty'
        );
    }
    
    public function getAdditionalAttributes()
    {
        $result = array(
            'stamp'
        );
        
        for($i = 1; $i <= self::MAX_FUNCTIONAL_ATTRIBUTES; $i++)
        {
            $result[] = 'qualite_fonctionnelle_'.$i;
        }
        
        return $result;
    }
    
    public function prepareProductData(Mage_Catalog_Model_Product $product, $isUpsell = false, $cartItemId = null, $productAsOption = false)
    {
        $result = array();
        
        if($product->getId())
        {
            $sizes = Mage::helper('dbm_share/image')->getSizes();
            
            $options = Mage::helper('dbm_share/image')->getOptionsForList();
            $imHelper = Mage::helper('dbm_utils/image');
            
            //Add inventory
            $result['inventory'] = array();
            
            $stockItem = $product->getStockItem();
            
            if($stockItem)
            {
                foreach($this->getStockAttributes() as $attribute)
                {
                    $result['inventory'][$attribute] = $stockItem->getData($attribute);
                }
            }
            
            //Values:
            $colorHexa = Mage::helper('dbm_utils/product')->getAttributeValueForProduct($product, 'color_hexa');
            
            $result['product_id'] = $product->getId();
            $result['set'] = $product->getAttributeSetId();
            $result['type'] = $product->getTypeId();
            $result['name'] = $product->getName();
            $result['sku'] = $product->getSku();
            $result['price'] = $product->getPrice();
            $result['special_price'] = ($result['type'] != 'bundle') ? $product->getSpecialPrice() : $product->getFinalPrice();
            $result['description'] = str_replace('<p>&nbsp;</p>', '', $product->getDescription());
            $result['category_ids'] = $product->getCategoryIds();
            $result['website_ids'] = $product->getWebsiteIds();
            $result['is_default'] = $product->getIsDefault() ? true : false;
            $result['color_hexa'] = $colorHexa  == 'Non' ? null : $colorHexa;
            $result['is_bundle_option'] = false;
            
            if($product->getTypeId() == 'simple')
            {
                //Test if cart has bundled product
                $cart = Mage::getModel('checkout/cart');
                $cartIds = array();
                if($cart)
                {
                    foreach($cart->getItems() as $item)
                    {
                        $cartIds[] = $item->getProduct()->getId();
                    }
                    
                    $cartIds = array_unique($cartIds);
                }
                
                $parentIds = current(Mage::getModel('bundle/product_type')->getParentIdsByChild($product->getId()));
                if(in_array($parentIds, $cartIds))
                {
                    $result['is_bundle_option'] = true;
                }
            }
            
            $estimateDate = Mage::helper('dbm_catalog')->getShippingEstimate();
            
            if($estimateDate)
            {
                $result['shipping_estimate'] = $estimateDate;
            }
            
            foreach($this->getImageAttributes() as $imageAttribute)
            {
                $url = null;
                if($product->getData($imageAttribute) != 'no_selection' && strlen($product->getData($imageAttribute)))
                {
                    $url = Mage::getBaseUrl('media').'catalog/product'.$product->getData($imageAttribute);
                }
                
                $result[$imageAttribute] = $url;
            }
            
            //Bundled product option thumbnail 100 for configurator
            if($product->hasData('image'))
            {
                $result['bundle_thumb'] = $imHelper->resizeProductImage($product->getImage(), $sizes['mobile_thumb'][0], $sizes['mobile_thumb'][1], $options);
            }
            
            
            
            if($productAsOption)
            {
                $testP = Mage::getModel('catalog/product')->load($product->getId());
                
                if($testP->getImageBundleMobile())
                {
                    $result['bundle_thumb'] = $imHelper->resizeProductImage($testP->getImageBundleMobile(), $sizes['mobile_thumb'][0], $sizes['mobile_thumb'][1], $options);
                }
            }

            //Add bundle option id
            if($product->hasData('selection_id'))
            {
                $result['selection_id'] = $product->getSelectionId();
                $result['option_id'] = $product->getOptionId();
            }
            
            //Add configurable attribute Data
            $parent = Mage::helper('dbm_utils/product')->getFirstParent($product);
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $parent)
            {
                $result['configurable_data'] = array();
                $attributes = $parent->getTypeInstance(true)->getConfigurableAttributes($parent);
                
                foreach($attributes as $attribute)
                {
                    $tmpConfData = array();
                    $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
                    
                    $tmpConfData['configurable_attribute_label'] = $attribute->getLabel();
                    $tmpConfData['configurable_attribute_code'] = $attributeCode;
                    $tmpConfData['configurable_attribute_id'] = $attribute->getAttributeId();
                    $tmpConfData['configurable_attribute_value_id'] = $product->getData($attributeCode);
                    $tmpConfData['configurable_attribute_value_label'] = Mage::helper('dbm_utils/product')->getAttributeValueForProduct($product, $attributeCode);
                    
                    $result['configurable_data'][] = $tmpConfData;
                }
            }

            foreach($this->getAdditionalAttributes() as $addAttribute)
            {
                if($product->getData($addAttribute))
                {
                    if(!is_array($result['additional_attributes']))
                    {
                        $result['additional_attributes'] = array();
                    }
                    
                    $result['additional_attributes'][$addAttribute]['key'] = $addAttribute;
                    
                    if(strstr($addAttribute, 'qualite_fonctionnelle_'))
                    {
                        if($product->getData($addAttribute))
                        {
                            $key = $addAttribute;
                            if($addAttribute == 'qualite_fonctionnelle_5')
                            {
                                $addAttribute = 'qualite_fonctionnelle_'.$product->getAttributeText('qualite_fonctionnelle_5');
                            }

                            $result['additional_attributes'][$key]['value'] = Mage::getBaseUrl('media').'pictos/'.$addAttribute.'.png';
                        }
                    }
                    else
                    {
                        $result['additional_attributes'][$addAttribute]['value'] = $product->getData($addAttribute);
                    }
                }
            }

            //Add media gallery
            $result['gallery'] = $this->getGallery($product, $result);
            
            
            //Add upsell
            $result['upsell_products'] = $this->getUpsell($product, $isUpsell);
            
            //Add product link
            $result['link'] =str_replace('index.php/', '', $product->getProductUrl());
            
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && !is_null($cartItemId))
            {
                $result['bundle_children'] = $this->getBundleCartChildren($product, $cartItemId);
            }
        }
        
        return $result;
    }
    
    public function getUpsell($product, $isUpsell)
    {
        //Upsell products 
        $upsell = $product->getUpSellProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToSort('position', Varien_Db_Select::SQL_ASC)
            ->addStoreFilter()
        ;
        
        $upsellResult = null;
        
        if(!$isUpsell && count($upsell))
        {
            $upsellResult = array();

            foreach($upsell as $upsellItem)
            {
                $product = Mage::getModel('catalog/product')->load($upsellItem->getId());
                $upsellResult[] = $this->prepareProductData($product, true);
            }
        }
        
        return $upsellResult;
    }
    
    public function getGallery($product, $productResult)
    {
        $gallery  = $product->getMediaGalleryImages();
        $galleryResult = array();

        $mobileConfiguratorImage = $product->getBundleButton();

        if(count($gallery))
        {
            foreach($gallery as $galleryItem)
            {
                if($galleryItem['file'] != $mobileConfiguratorImage)
                {
                    $item = $galleryItem->getUrl();

                    if($productResult['image'] && $productResult['image'] != $item)
                    {
                        $galleryResult[] = $item;
                    }

                }
            }
        }

        return $galleryResult;
    }
    
    public function getBundleCartChildren($product, $cartItemId)
    {
        $cart = Mage::getModel('checkout/cart');
        $result = array();
        
        if($cart)
        {
            foreach($cart->getItems() as $item)
            {
                if($item->getId() == $cartItemId)
                {
                    $result = $this->_getCartItemOptionDataForProduct($item);
                }
            }
        }
        
        return $result;
    }
    
    protected function _getSelectionQty($selectionId, $product)
    {
        if ($selectionQty = $product->getCustomOption('selection_qty_' . $selectionId)) {
            return $selectionQty->getValue();
        }
        return 0;
    }
    
    protected function _getSelectionFinalPrice($selectionProduct, $product, $item)
    {
        $bundleProduct = $product;
        return $bundleProduct->getPriceModel()->getSelectionFinalPrice(
            $bundleProduct, $selectionProduct,
            $item->getQty() * 1,
            $this->_getSelectionQty($selectionProduct->getSelectionId(), $product)
        );
    }
    
    protected function _getCartItemOptionDataForProduct($bundleItem)
    {
        $product = $bundleItem->getProduct();
        
        $optionsQuoteItemOption =  $bundleItem->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = unserialize($optionsQuoteItemOption->getValue());
        
        $typeInstance = $product->getTypeInstance(true);
        $result = array();
        
        if($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $bundleItem->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $product
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $result[] = array(
                            'option_id' => $bundleOption->getOptionId(),
                            'selection_id' => $bundleSelection->getSelectionId(),
                            'product_id' => $bundleSelection->getProductId()
                        );
                    }
                }
            }
        }
        
        return $result;
    }
}
