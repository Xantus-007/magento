<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ aTjTWiTZwjiyDTga('1b6b8065d7f5ed936d98ea1d7d3f757f');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    // override parent
    protected function _prepareCollection()
    {
        $store = $this->_getStore(); // aitoc code
        $iWebsiteId = $store->getWebsiteId(); // aitoc code
        $websites = Mage::getModel('core/website')->getCollection();
        
        if (!$iWebsiteId)
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
        }
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $iWebsiteId, // aitoc code
                'left');
#d($iWebsiteId);

        foreach($websites as $website)
        {
            $collection->joinField('qty_'.$website->getCode(),
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(), // aitoc code
                'left');

            $collection->joinField('status_'.$website->getCode(),
                'aitquantitymanager/stock_status', // aitoc code
                'stock_status',
                'product_id=entity_id',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(),
                'left');
        }

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $collection->getSelect()->group('entity_id');
        
        $collection->getSelect()->distinct(true);
        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection(); // aitoc code
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
} } 