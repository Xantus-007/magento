<?php
/**
 * Monbento_FeaturedProducts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @copyright  Copyright (c) 2010 Anthony Charrex
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @author     Anthony Charrex <anthony.charrax@gmail.com>
 */
class Monbento_Bestsellers_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract
{

    protected $_productsCount = null;


    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 3600,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
            'cache_key'					=> $this->getCacheKey()
        ));
    }

    /**
     * Retrieve Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        return 'MONBENTO_BESTSELLERS_' . Mage::app()->getStore()->getId()
            . '_' . Mage::getDesign()->getPackageName()
            . '_' . Mage::getDesign()->getTheme('template')
            . '_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_' . md5($this->getTemplate());
    }

    /**
     * Prepare collection with new products and applied page limits.
     *
     * return Monbento_FeaturedProducts_Block_Featured
     */
    protected function _beforeToHtml()
    {
        $storeId = (int) Mage::app()->getStore()->getId();
 
        // Date
        $date = new Zend_Date();
        $fromDate = date("Y-m-d", strtotime('-30 days'));
        $toDate = date("Y-m-d");
 
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setPageSize(5);
 
        $collection->getSelect()
            ->joinLeft(
                array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
                array('SUM(aggregation.qty_ordered) AS sold_quantity')
            )
            ->group('e.entity_id')
            ->order(array('sold_quantity DESC', 'e.created_at'));
 
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        $products=new Varien_Data_Collection();
	foreach ($collection as $value) {
            $products->addItem(Mage::getModel('catalog/product')->getCollection()->addUrlRewrite()->addAttributeToSelect(array('name', 'small_image'))->addAttributeToFilter('entity_id', $value['entity_id'])->getFirstItem());
	}

        $this->setProductCollection($products);

        return parent::_beforeToHtml();
    }



    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount() {
        if (is_null($this->_productsCount)) {
            $this->_productsCount = count($this->getProductCollection());
        }
        return $this->_productsCount;
    }

}