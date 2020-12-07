<?php

class Dbm_TagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSlideClickData($slide, $position)
    {
        if($slide)
        {
            $position++;
            $listName = ($_category = Mage::registry('current_category')) ? $_category->getName() : 'Home';

            $data = array(
                "event" => "promotionClick", 
                "ecommerce" => array(
                    "promoClick" => array(
                        "actionField" => array(
                            "list" => 'Promotions ' . $listName
                        ),
                        "promotions" => array(
                            array(
                                "id" => $slide->getEntityId(), 
                                "name" => $slide->getPostTitle(), 
                                "creative" => $slide->getImage(), 
                                "position" => 'slider_slot'.$position
                            )
                        )
                    )
                )
            );

            return $this->encodeLayerData($data);
        }
        
        return null;
    }
    
    public function getProductClickData($product, $position)
    {
        if($product)
        {
            $position++;
            $productCats = $product->getCategoryIds();
            $catName = (Mage::registry('current_category')) ? Mage::registry('current_category')->getName() : Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();
            
            $data = array(
                "event" => "productClick",
                "ecommerce" => array(
                    "click" => array(
                        "actionField" => array(
                            "list" => Mage::registry('gtm_listname')
                        ),
                        "products" => array(
                            array(
                                "name" => (string) $product->getName(),
                                "id" => (string) $product->getId(),
                                "price" => (string) $product->getFinalPrice(),
                                "category" => (string) $catName,
                                "position" => $position
                            )
                        )
                    )
                )
            );
            
            return $this->encodeLayerData($data);
        }
        
        return null;
    }
    
    public function getAddToCartClickData($product)
    {
        if($product)
        {
            $productCats = $product->getCategoryIds();
            $catName = (Mage::registry('current_category')) ? Mage::registry('current_category')->getName() : Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();
            
            $data = array(
                "event" => "addToCart",
                "ecommerce" => array(
                    "currencyCode" => "EUR",
                    "add" => array(
                        "products" => array(
                            array(
                                "name" => (string) $product->getName(),
                                "id" => (string) $product->getId(),
                                "price" => (string) $product->getFinalPrice(),
                                "category" => (string) $catName,
                                "variant" => "",
                                "quantity" => 1
                            )
                        )
                    )
                )
            );
            
            return $this->encodeLayerData($data);
        }
        
        return null;
    }
    
    public function getCheckoutClickData($option, $step)
    {
        if($option)
        {
            $data = array(
                "event" => "checkoutOption", 
                "ecommerce" => array(
                    "promoClick" => array(
                        "checkout_option" => array(
                            "actionField" => array(
                                "step" => $step, 
                                "option" => $option
                            )
                        )
                    )
                )
            );

            return $this->encodeLayerData($data);
        }
        
        return null;
    }
    
    public function encodeLayerData($data)
    {
        return Zend_Json_Encoder::encode($data);
    }

}
