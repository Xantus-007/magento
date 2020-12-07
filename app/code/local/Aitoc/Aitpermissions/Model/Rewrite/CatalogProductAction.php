<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/CatalogProductAction.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ QqjZSDWgfgCNoIei('608fc4b06eba65f9ebc6df9ffbfbf8aa'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_CatalogProductAction extends Mage_Catalog_Model_Product_Action
{
    public function updateAttributes($productIds, $attrData, $storeId)
    {
        if (isset($attrData['status']) &&
            $this->_isUpdatingStatus() &&
            Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() &&
            Mage::getStoreConfig('admin/su/enable')
        )
        {
            if ($attrData['status'] == Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING)
            {
                Mage::throwException(Mage::helper('core')->__('This status cannot be used in mass action'));
                return $this;
            }
			
			foreach ($this->_getProductIdsToApprove($productIds) as $productId)
			{
			   Mage::getModel('aitpermissions/approve')->approve($productId, $attrData['status']);
			}
        }
        
        return parent::updateAttributes($productIds, $attrData, $storeId);
    }

    private function _isUpdatingStatus()
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();

        return ($controllerName == 'catalog_product' && $actionName == 'massStatus') ||
            ($controllerName == 'catalog_product_action_attribute' && $actionName == 'save');
    }

    private function _getProductIdsToApprove($productIds)
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($productIds)
            ->addAttributeToFilter('status', array('neq' => Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING));

        $productIdsToApprove = array();

        foreach ($productCollection as $product)
        {
            $productIdsToApprove[] = $product->getId();
        }

        return $productIdsToApprove;
    }
} } 