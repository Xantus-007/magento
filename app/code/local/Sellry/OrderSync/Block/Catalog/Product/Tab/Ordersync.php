<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Block_Catalog_Product_Tab_Ordersync
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    
    public function _construct() {
        parent::_construct();
        $this->setTemplate('ordersync/catalog/product/tab/ordersync.phtml');
    }

    public function getTabLabel() {
        return $this->__('Order Sync');
    }
    
    public function getTabTitle() {
        return $this->__('Click here to view tab content');
    }
    
    public function canShowTab() {
        return !$this->isNew() && !$this->getProduct()->isComposite();
    }
    
    public function isHidden() {
        return false;
    }
    
    public function getTabClass() {
        return 'ajax';
    }
    
    public function getSkipGenerateContent() {
        return false;
    }
    
    public function getTabUrl() {
        return null;
    }

    public function getProduct() {
        return Mage::registry('product');
    }

    public function isNew() {
        if (Mage::registry('product')->getId()) {
            return false;
        }
        return true;
    }

    public function getGlobalConfigValue($field) {
        return Mage::helper('ordersync')->getGeneralConfig($field);
    }

    public function getInventorySettings() {
        $productId = $this->getProduct()->getId();
        $productInventorySettings = Mage::getModel('ordersync/inventory')->load($productId, 'product_id');
        if ($productInventorySettings->getId()) {
            //using default => populate default value from general settings, not last used
            if ($productInventorySettings->getAllowStockUpdateDefault()) {
                $productInventorySettings->setAllowStockUpdate($this->getGlobalConfigValue('updateskus'));
            }
            if ($productInventorySettings->getAllowExportDefault()) {
                $productInventorySettings->setAllowExport($this->getGlobalConfigValue('exportskus'));
            }
            return $productInventorySettings;
        }

        $settings = new Varien_Object();
        $settings
            ->setAllowExport($this->getGlobalConfigValue('exportskus'))
            ->setAllowExportDefault(1)
            ->setAllowStockUpdate($this->getGlobalConfigValue('updateskus'))
            ->setAllowStockUpdateDefault(1);

        return $settings;
    }
}