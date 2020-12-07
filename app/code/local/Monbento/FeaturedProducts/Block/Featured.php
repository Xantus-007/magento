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
 
class Monbento_FeaturedProducts_Block_Featured extends Mage_Catalog_Block_Product_Abstract
{

    protected $_productsCount = null;

    const DEFAULT_PRODUCTS_COUNT = 5;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 86400,
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
        return 'MONBENTO_FEATURED_PRODUCT_' . Mage::app()->getStore()->getId()
            . '_' . Mage::getDesign()->getPackageName()
            . '_' . Mage::getDesign()->getTheme('template')
            . '_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_' . md5($this->getTemplate())
            . '_' . $this->getProductsCount()
            . '_' . $this->getCategoryTitle();
    }

    /**
     * Prepare collection with new products and applied page limits.
     *
     * return Monbento_FeaturedProducts_Block_Featured
     */
    protected function _beforeToHtml()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('is_featured', 1)
			->addAttributeToFilter('entity_id', array('in'=>array(substr($this->getData('id_path1'),8),substr($this->getData('id_path2'),8),substr($this->getData('id_path3'),8),substr($this->getData('id_path4'),8))))
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;

        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }
	
    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Monbento_FeaturedProducts_Block_Featured
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
	
    public function getCategoryId()
    {
		if ($this->getData('id_path_category')) {
			return substr($this->getData('id_path_category'),9);
		}

    }
	
    public function getProductOrder($idproduct)
    {
		for ($i=1;$i<5;$i++) {
		  $dataid = substr($this->getData('id_path'.$i),8);
		  $dataid = substr($dataid,0,strcspn($dataid,"/"));
		  if ($dataid==$idproduct) {
		    return $i;
		  }
		}

    }
	
    public function getCategoryTitle()
    {
		if ($this->getData('id_path_category')) {
			$_category = Mage::getModel('catalog/category')->load($this->getCategoryId());
			if($_category) {
			return $_category->getName();
			}
		}

    }
	
    public function getCategoryLink()
    {
		if ($this->getData('id_path_category')) {
			$_category = Mage::getModel('catalog/category')->load($this->getCategoryId());
			if($_category) {
			return $_category->getUrl();
			}
		}

    }
	
    public function getCategoryIcon()
    {
		if ($this->getData('id_path_category')) {
			$_category = Mage::getModel('catalog/category')->load($this->getCategoryId());
			if($_category->getUrlKey()) {
			return ' style="background-image:url(/skin/frontend/default/monbento/images/categories/'.$_category->getUrlKey().'.png);"';
			}
		}

    }
	
}
