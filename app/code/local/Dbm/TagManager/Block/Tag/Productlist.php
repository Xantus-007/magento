<?php

class Dbm_TagManager_Block_Tag_ProductList extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $this->ecommerceData = array("event" => "impressions", "ecommerce" => array("currencyCode" => "EUR", "impressions" => array()));

        $collection = ($this->hasProducts()) ? $this->getProducts() : $this->_getProductCollection();
        if($collection)
        {
            $productsData = array();
            $position = 1;
            foreach($collection as $product)
            {
                if($product->getStatus() == 1)
                {
                    $productCats = $product->getCategoryIds();
                    $listName = $this->_getProductListName(array_reverse($productCats));
                    $catName = (Mage::registry('current_category')) ? Mage::registry('current_category')->getName() : Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();

                    $productsData[] = array(
                        "name" => (string) $product->getName(),
                        "id" => (string) $product->getId(),
                        "price" => (float) $product->getFinalPrice(),
                        "category" => (string) $catName,
                        "list" => (string) $listName,
                        "position" => $position
                    );

                    $position++;
                }
            }

            if(!empty($productsData)) 
            {
                if(Mage::registry('gtm_listname')) Mage::unregister('gtm_listname');
                Mage::register('gtm_listname', $listName);
                $this->setLayerData($productsData);
            }
        }
    }

    protected function _getProductCollection()
    {
        /** @var Mage_Catalog_Block_Product_List $productListBlock */
        $productListBlock = Mage::app()->getLayout()->getBlock('product_list');

        if (empty($productListBlock)) {
            return null;
        }

        $params = Mage::app()->getRequest()->getParams();

        // Fetch the current collection from the block and set pagination
        $gtmCollection = (count($params) == 1 || (count($params) == 2 && isset($params['isLayerAjax']))) ? $productListBlock->getLoadedProductCollection() : clone $productListBlock->getLoadedProductCollection();

        return $gtmCollection;
    }
    
    protected function _getProductListName($catIds)
    {
        if($this->hasBlockName())
        {
            return $this->getBlockName();
        }
        else
        {
            $list = array();
            foreach($catIds as $catId)
            {
                if(count($list) < 2)
                {
                    $category = Mage::getModel('catalog/category')->load($catId);
                    $list[] = $category->getName();
                }
            }

            return implode(" - ", array_reverse($list));
        }
    }
}