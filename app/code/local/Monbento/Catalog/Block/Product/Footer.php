<?php 
class Monbento_Catalog_Block_Product_Footer extends Mage_Catalog_Block_Product_Abstract  

{  
    public $_collection;
    public $_catName;
    private $_theCat;

      public function setCat($category) {  
        $this->_theCat = $category;
    }

    public function getCatName() {  
        return $this->_catName;
    }

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 157680000,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    /**
     * Retrieve Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        return 'CATALOG_PRODUCT_FOOTER_' . Mage::app()->getStore()->getId()
            . '_' . Mage::getDesign()->getPackageName()
            . '_' . Mage::getDesign()->getTheme('template')
            . '_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_' . md5($this->getTemplate())
            . '_' . $this->getProductsCount();
    }

    protected function _getProductCollection() {  
        $storeId    = Mage::app()->getStore()->getId();  
        $product    = Mage::getModel('catalog/product');  
        $category   = Mage::getModel('catalog/category')->load($this->_theCat);
        $this->_catName = $category->getName();

        $visibility = array(  
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG  
        );  

        $products   = $product->setStoreId($storeId)  
                              ->getCollection()  
            ->addAttributeToFilter('status', 1)  
            ->addAttributeToFilter('visibility', $visibility)  
            ->addAttributeToSelect(array('name'), 'inner') //you need to select “name” or you won't be able to call getName() on the product 
            ->setOrder('name', 'asc')
        ;  

        $this->_collection = $products;  
        return $this->_collection;  
    }  

    public function getCurrentCategory() {  
        return Mage::getModel('catalog/category')->load($this->_theCat);  
    }  

    public function getProductCollection() {  
        return $this->_getProductCollection();  
    } 
	
}
?>