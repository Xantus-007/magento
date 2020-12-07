<?php

class Dbm_Catalog_Model_Catalog_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
{
    private $_mendatoryAttributes = array(
        'name'
    );

    public function getProductInfo($productId, $store = null, $attributes = null, $identifierType = null, $cartItemId = null)
    {
        Mage::app()->setCurrentStore($this->_getStoreId($store));

        $helper = Mage::helper('dbm_utils/product');
        foreach($this->_mendatoryAttributes as $attribute)
        {
            if(!is_null($attributes) && !in_array($attribute, $attributes->attributes))
            {
                $attributes->attributes[] = $attribute;
            }
        }
        
        $result = parent::info($productId, $store, $attributes, $identifierType);

        $product = Mage::getModel('catalog/product')->load($result['product_id']);
        $product->setStoreView($store);

        if($product->getTypeId() == 'configurable')
        {
            $childrenResult = array();
            
            $children = $helper->getDeclinaisons($product);
            $i = 0;
            
            foreach($children as $child)
            {
                $tmpChild = Mage::helper('dbm_catalog/api')->prepareProductData($child, false, $cartItemId);
                $childrenResult[$tmpChild['price'].$i] = $tmpChild;
                $i++;
            }
            
            ksort($childrenResult);
            
            if(count($childrenResult))
            {
                $result['children'] = $childrenResult;
            }
        }

        $result += Mage::helper('dbm_catalog/api')->prepareProductData($product, false, $cartItemId);

        return $result;
    }
}