<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogInventoryStockItem extends Mage_CatalogInventory_Model_Stock_Item
{
    /**
     * Retrieve Manage Stock data wrapper
     *
     * @return int
     */
    public function getManageStock()
    {
        if(is_object($this->getProduct())) {
            if ($this->getProduct()->isConfigurable() == true) {
                if ($this->getProduct()->getParentProductId() != null) {
                    return 0;
                }
            }
        }

        return parent::getManageStock();
    }

    /**
     * Retrieve Minimum Qty Allowed in Shopping Cart or NULL when there is no limitation
     *
     * @return float|null
     */
    public function getMinSaleQty()
    {
        if(is_object($this->getProduct())) {
            if ($this->getProduct()->isConfigurable() == true) {
                if($this->getProduct()->getParentProductId() != null) {
                    return null;
                }
            }
        }

        return parent::getMinSaleQty();
    }

    /**
     * Check quantity
     *
     * @param   decimal $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        if(is_object($this->getProduct())) {
            if ($this->getProduct()->isConfigurable() == true) {
                if($this->getProduct()->getParentProductId() != null) {
                    return true;
                }
            }
        }

        return parent::checkQty($qty);
    }

    /**
     * Checking quote item quantity
     *
     * Second parameter of this method specifies quantity of this product in whole shopping cart
     * which should be checked for stock availability
     *
     * @param mixed $qty quantity of this item (item qty x parent item qty)
     * @param mixed $summaryQty quantity of this product
     * @param mixed $origQty original qty of item (not multiplied on parent item qty)
     * @return Varien_Object
     */
    public function checkQuoteItemQty($qty, $summaryQty, $origQty = 0)
    {
        $result = new Varien_Object();
        $result->setHasError(false);

        if (!is_numeric($qty)) {
            $qty = Mage::app()->getLocale()->getNumber($qty);
        }

        /**
         * Check quantity type
         */
        $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

        if (!$this->getIsQtyDecimal()) {
            $result->setHasQtyOptionUpdate(true);
            $qty = intval($qty);

            /**
             * Adding stock data to quote item
             */
            $result->setItemQty($qty);

            if (!is_numeric($qty)) {
                $qty = Mage::app()->getLocale()->getNumber($qty);
            }

            $origQty = intval($origQty);
            $result->setOrigQty($origQty);
        }

        if ($this->getMinSaleQty() && $qty < $this->getMinSaleQty()) {
            if ($this->getProduct()->isConfigurable() == true) {
                if($this->getProduct()->getParentProductId() == null) {
                    $result->setHasError(true)
                        ->setMessage(
                            Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1)
                        )
                        ->setErrorCode('qty_min')
                        ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                        ->setQuoteMessageIndex('qty');
                }
            }

            return $result;
        }

        if ($this->getMaxSaleQty() && $qty > $this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage(
                    Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1)
                )
                ->setErrorCode('qty_max')
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        $result->addData($this->checkQtyIncrements($qty)->getData());
        if ($result->getHasError()) {
            return $result;
        }

        if (!$this->getManageStock()) {
            return $result;
        }

        if (!$this->getIsInStock()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('This product is currently out of stock.'))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products are currently out of stock.'))
                ->setQuoteMessageIndex('stock');
            $result->setItemUseOldQty(true);
            return $result;
        }

        if (!$this->checkQty($summaryQty) || !$this->checkQty($qty)) {
            if ($this->getProduct()->isConfigurable() == false) {
                $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProductName());
                $result->setHasError(true)
                    ->setMessage($message)
                    ->setQuoteMessage($message)
                    ->setQuoteMessageIndex('qty');
            }

            return $result;
        } else {
            if (($this->getQty() - $summaryQty) < 0) {
                if ($this->getProductName()) {
                    if ($this->getIsChildItem()) {
                        $backorderQty = ($this->getQty() > 0) ? ($summaryQty - $this->getQty()) * 1 : $qty * 1;
                        if ($backorderQty > $qty) {
                            $backorderQty = $qty;
                        }

                        $result->setItemBackorders($backorderQty);
                    } else {
                        $orderedItems = $this->getOrderedItems();
                        $itemsLeft = ($this->getQty() > $orderedItems) ? ($this->getQty() - $orderedItems) * 1 : 0;
                        $backorderQty = ($itemsLeft > 0) ? ($qty - $itemsLeft) * 1 : $qty * 1;

                        if ($backorderQty > 0) {
                            $result->setItemBackorders($backorderQty);
                        }

                        $this->setOrderedItems($orderedItems + $qty);
                    }

                    if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                        if (!$this->getIsChildItem()) {
                            $result->setMessage(
                                Mage::helper('cataloginventory')->__('This product is not available in the requested quantity. %s of the items will be backordered.', ($backorderQty * 1))
                            );
                        } else {
                            $result->setMessage(
                                Mage::helper('cataloginventory')->__('"%s" is not available in the requested quantity. %s of the items will be backordered.', $this->getProductName(), ($backorderQty * 1))
                            );
                        }
                    } elseif (Mage::app()->getStore()->isAdmin()) {
                        if ($this->getProduct()->isConfigurable() == false) {
                            $result->setMessage(
                                Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProductName())
                            );
                        }
                    }
                }
            } else {
                if (!$this->getIsChildItem()) {
                    $this->setOrderedItems($qty + (int)$this->getOrderedItems());
                }
            }
        }

        return $result;
    }
}


if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ DIBIEhIjqBhreImD('69f016db188ed3bd43c30ec138b020a5');
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

class Wizkunde_ConfigurableBundle_Model_CatalogInventory_Stock_Item extends Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogInventoryStockItem
{
    // overide parent
    protected function _construct()
    {
        Mage::getModel('aitquantitymanager/moduleObserver')->onAitocModuleLoad();
        
///ait/        $this->_init('cataloginventory/stock_item');
        $this->_init('aitquantitymanager/stock_item');
    }

    // overide parent
    public function loadByProduct($product, $iWebsiteId = 0)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        
// start aitoc code  

        $iStoreId   = 0;

        if ($iStoreId = Mage::registry('aitoc_order_refund_store_id')) // fix for refund
        {
            
        }
        else 
        {
            $iStoreId = Mage::registry('aitoc_order_create_store_id'); // fix for create
        }
        
        if (!$iStoreId AND $controller = Mage::app()->getFrontController()) 
        {
            $oRequest = $controller->getRequest();
            if ($oRequest->getParam('website')) {
                $storeIds = Mage::app()->getWebsite($oRequest->getParam('website'))->getStoreIds();
                $iStoreId = array_pop($storeIds);
            } else if ($oRequest->getParam('group')) {
                $storeIds = Mage::app()->getGroup($oRequest->getParam('group'))->getStoreIds();
                $iStoreId = array_pop($storeIds);
            } else if ($oRequest->getParam('store')) {
                $iStoreId = (int)$oRequest->getParam('store');
            } elseif ($oRequest->getParam('store_id')) {
                $iStoreId = (int)$oRequest->getParam('store_id');
            } elseif (Mage::getSingleton('adminhtml/session_quote')->getStoreId()) {
                $iStoreId = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
            } else {
                $iStoreId = '';
            }
        }  
        
        if (!$iStoreId) 
        {
            $iStoreId = $this->getStoreId(); 
        }
        
        if (!$iWebsiteId AND $iStoreId)
        {
            $store = Mage::app()->getStore($iStoreId);
            
#            Mage::app()->getStores(true, true);
            
            $iWebsiteId = $store->getWebsiteId();
        }
        if (!$iWebsiteId) 
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId(); // default
        }
// finish aitoc code        
        
#        $this->_getResource()->loadByProductId($this, $product);
        $this->_getResource()->loadByProductId($this, $product, $iWebsiteId); // aitoc code        

        $this->setOrigData();
        return $this;
    }

// start aitoc code    
    public function loadByProductWebsite($product, $iWebsiteId)
    {
        if (!$iWebsiteId) return false;
        
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        
        $this->_getResource()->loadByProductId($this, $product, $iWebsiteId); // aitoc code        

        $this->setOrigData();
        return $this;
    }

    public function getProductItemHash($iProductId)
    {
        return $this->_getResource()->getProductItemHash($iProductId);
    }
    
    public function getProductDefaultItem($iProductId)
    {
        return $this->_getResource()->getProductDefaultItem($iProductId);
    }
// finish aitoc code    

    // overide parent
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getId() || !$this->getProductId()) 
        {
// start aitoc code            
            $iWebsiteId = $product->getStore()->getWebsiteId();
                
            $iHiddenWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();

            if (!$iWebsiteId)
            {
                $iWebsiteId = 1; // default
                
                if (!$this->getId())
                {
                    $iWebsiteId = $iHiddenWebsiteId;
                }
            }
            
            $oAitocItem = Mage::getResourceModel('aitquantitymanager/stock_item');
            $oAitocItem->loadByProductId($this, $product->getId(), $iWebsiteId);
            if (!$this->getId() AND $iWebsiteId != $iHiddenWebsiteId)
            {
                $iWebsiteId = $iHiddenWebsiteId; // default
                $oAitocItem->loadByProductId($this, $product->getId(), $iWebsiteId);
                $this->setId(null); 
            }
        }
// finish aitoc code

        $this->setProduct($product);
        $product->setStockItem($this);
        $product->setIsInStock($this->getIsInStock());
        Mage::getSingleton('cataloginventory/stock_status')
            ->assignProduct($product, $this->getStockId(), $this->getStockStatus());
        return $this;
    }


// start aitoc code
    public function getStoreById($id)
    {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_storesIdCode[$id])) {
            return $this->getStoreByCode($this->_storesIdCode[$id]);
        }
        return false;
    }

// finish aitoc code
    
    
    /**
     * Before save prepare process
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _beforeSave()
    {
        
// start aitoc  code      

        if ($iWebsiteId = $this->getSaveWebsiteId())
        {
            // get from observer
        }
        else 
        {
            if ($iStoreId = Mage::registry('aitoc_order_refund_store_id')) // fix for refund
            {
                $store = Mage::app()->getStore($iStoreId);
                $iWebsiteId = $store->getWebsiteId();
                
                Mage::unregister('aitoc_order_refund_store_id');
            }
            elseif ($iStoreId = Mage::registry('aitoc_order_create_store_id')) // fix for refund
            {
                $store = Mage::app()->getStore($iStoreId);
                $iWebsiteId = $store->getWebsiteId();
            }
            elseif (Mage::registry('aitoc_api_update_website_id'))
            {
                $iWebsiteId = Mage::registry('aitoc_api_update_website_id');}
            else 
            {
                $iWebsiteId = 0;
            }
        }
        
        if ($controller = Mage::app()->getFrontController()) 
        {
            $oRequest = $controller->getRequest();
            $iStoreId = (int)$oRequest->getParam('store');
        }
        else 
        {
            $iStoreId = 0;
        }
        
        if (!$iStoreId) 
        {
            $iStoreId = $this->getStoreId();
        }
        
        if (!$iWebsiteId AND $iStoreId)
        {
            $store = Mage::app()->getStore($iStoreId);
            
            $iWebsiteId = $store->getWebsiteId();
        }
        
        if (!$iWebsiteId) 
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId(); // default
        }

        if (!Mage::registry('reindex_in_progress') && !Mage::registry('aitoc_api_update'))
        {
            // do not replace website_id if update from API
            $this->setWebsiteId($iWebsiteId);
        }
// finish aitoc code
        
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }
        $isQty = Mage::helper('catalogInventory')->isQty($typeId);

        if ($isQty) {
            if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO
                && $this->getQty() <= $this->getMinQty()) {
                $this->setIsInStock(false)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ((float)$this->getQty() < $this->getNotifyStockQty()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        }
        else {
            $this->setQty(0);
        }

        Mage::dispatchEvent('cataloginventory_stock_item_save_before', array('item' => $this));
        
        return $this;
    }
    
    // override parent
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );#
        return $this;
    }    
    
} }

