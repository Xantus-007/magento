<?php

class Dbm_Catalog_Model_Catalog_Category_Api_V2 extends Mage_Catalog_Model_Category_Api_V2
{
    protected $_currentStore = 0;
    
    public function tree($parentId = null, $store = null)
    {
        Mage::log('CALLING STORE FOR TREE : '.$store, null, 'api.xml');
        $this->_currentStore = $store;
        Mage::app()->setCurrentStore($this->_currentStore);
        
        if (is_null($parentId) && !is_null($store)) {
            $parentId = Mage::app()->getStore($this->_getStoreId($store))->getRootCategoryId();
        } elseif (is_null($parentId)) {
            $parentId = 1;
        }

        /* @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */
        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();

        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('include_in_menu', 1);

        $tree->addCollectionData($collection, true);

        return $this->_nodeToArray($root);
    }
    
    public function assignedProducts($categoryId, $store = null)
    {
        //$items = parent::assignedProducts($categoryId, $storeId);
        $category = $this->_initCategory($categoryId);
        
        Mage::log('CALLING STORE FOR PRODUCTS : '.$store, null, 'api.xml');
        
        $this->_currentStore = $store;
        Mage::app()->setCurrentStore($this->_currentStore);
        
        $storeId = $this->_getStoreId($store);
        $collection = $category->setStoreId($storeId)->getProductCollection()
            ->addAttributeToSelect('visibility')
            ////->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)
            ->addAttributeToFilter('visibility', array('in' => array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            )))
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        ($storeId == 0)? $collection->addOrder('position', 'asc') : $collection->setOrder('position', 'asc');;

        $items = array();

        foreach ($collection as $product) 
        {
            $items[] = array(
                'product_id' => $product->getId(),
                'type'       => $product->getTypeId(),
                'set'        => $product->getAttributeSetId(),
                'sku'        => $product->getSku(),
                'position'   => $product->getPosition()
            );
        }

        $result = array();
        $model = Mage::getModel('dbm_catalog/catalog_product_api_v2');

        foreach($items as $product)
        {
            $result[] = $model->getProductInfo($product['product_id'], $store, $attributes, 'id');
        }

        return $result;
    }
    
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        $cat = Mage::getModel('catalog/category')->load($node->getId());
        
        // Only basic category data
        $result = array();
        $result['category_id'] = $cat->getId();
        $result['parent_id']   = $cat->getParentId();
        $result['name']        = $cat->getName();
        $result['is_active']   = $cat->getIsActive();
        $result['position']    = $cat->getPosition();
        $result['level']       = $cat->getLevel();
        $result['include_in_menu'] = $cat->getIncludeInMenu();
        $result['legend']      = $cat->getLegend();
        
        //Customized bento must not appear
        if($cat->getId() == Dbm_Catalog_Helper_Data::CUSTOM_BENTO_CAT_01 || !$result['include_in_menu'])
        {
            $result['is_active'] = false;
        }
        
        if($cat->getMobileImage())
        {
            $result['mobile_image'] = Mage::getBaseUrl('media').'catalog/category/'.$cat->getMobileImage();
        }

        $result['children']    = array();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }
}