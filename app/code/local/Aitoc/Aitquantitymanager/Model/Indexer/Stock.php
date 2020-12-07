<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ BhmhaohgimoDMhrB('91928f6001144f2f88ff60d8c31bd72f');
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


class Aitoc_Aitquantitymanager_Model_Indexer_Stock extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_CatalogInventory_Model_Stock_Item::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Config_Data::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Related config settings
     *
     * @var array
     */
    protected $_relatedConfigSettings = array(
        Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
        Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
#        $this->_init('cataloginventory/indexer_stock');
        $this->_init('aitquantitymanager/indexer_stock');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('cataloginventory')->__('Stock status');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('cataloginventory')->__('Index product stock status');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        $resultKey  = 'cataloginventory_stock_match_result';
        if (isset($data[$resultKey])) {
            return $data[$resultKey];
        }

        $result = null;
        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            /* @var $store Mage_Core_Model_Store */
            $store = $event->getDataObject();
            if ($store->isObjectNew()) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            /* @var $storeGroup Mage_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Config_Data::ENTITY) {
            $configData = $event->getDataObject();
            $path = $configData->getPath();
            if (in_array($path, $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData($resultKey, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getEntity()) {
            case Mage_CatalogInventory_Model_Stock_Item::ENTITY:
                $this->_registerCatalogInventoryStockItemEvent($event);
                break;

            case Mage_Catalog_Model_Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('cataloginventory_stock_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
            case Mage_Core_Model_Config_Data::ENTITY:
                $event->addNewData('cataloginventory_stock_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

                if ($event->getEntity() == Mage_Core_Model_Config_Data::ENTITY) {
                    $configData = $event->getDataObject();
                    if ($configData->getPath() == Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK) {
                        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price')
                            ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_attribute')
                            ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                    }
                }
                break;
        }
    }

    /**
     * Register data required by catalog product processes in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $product = $event->getDataObject();
                if ($product && $product->getStockData()) {
                    $product->setForceReindexRequired(true);
                }
                break;
            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;
        }
    }

    /**
     * Register data required by cataloginventory stock item processes in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogInventoryStockItemEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $this->_registerStockItemSaveEvent($event);
                break;
        }
    }

    /**
     * Register data required by stock item save process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerStockItemSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $object Mage_CatalogInventory_Model_Stock_Item */
        $object      = $event->getDataObject();

//        $properties = array(
//            'manage_stock',
//            'use_config_manage_stock',
//            'is_in_stock'
//        );

//        $reindexStock = false;
//        foreach ($properties as $property) {
//            if ($event->dataHasChangedFor($property)) {
//                $reindexStock = true;
//                break;
//            }
//        }

        $event->addNewData('reindex_stock', 1);
        $event->addNewData('product_id', $object->getProductId());

//        if ($reindexStock && !Mage::helper('cataloginventory')->isShowOutOfStock()) {
//        }

        return $this;
    }

    /**
     * Register data required by product delete process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_stock_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by product mass action process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        $attributes   = array(
            'status'
        );
        $reindexStock = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexStock = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexStock = true;
        }

        // register affected products
        if ($reindexStock) {
            $event->addNewData('reindex_stock_product_ids', $actionObject->getProductIds());
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['cataloginventory_stock_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['cataloginventory_stock_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
} } 