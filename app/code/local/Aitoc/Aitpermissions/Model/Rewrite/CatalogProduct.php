<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/CatalogProduct.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('fd4023b0bf566412c303952fc13fce21'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct extends Mage_Catalog_Model_Product
{
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled()
            && Mage::getStoreConfig('admin/su/enable')
            && !$this->getCreatedAt())
        {
            $this->setStatus(Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING);
            Mage::getModel('aitpermissions/notification')->send($this);
        }
        
        if ($this->getId() && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getId(), $this->getStatus());
        }

        $request = Mage::app()->getRequest();

        if ($request->getPost('simple_product') && $request->getQuery('isAjax') && $role->isScopeStore())
        {
            $this->_setParentCategoryIds($request->getParam('product'));
        }
        
        return $this;
    }

    private function _setParentCategoryIds($parentId)
    {
        $configurableProduct = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->load($parentId);

        if ($configurableProduct->isConfigurable())
        {
            if (!$this->getData('category_ids'))
            {
                $categoryIds = (array)$configurableProduct->getData('category_ids');
                if ($categoryIds)
                {
                    $this->setData('category_ids', $categoryIds);
                }
            }
        }
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        
        if ($this->getData('entity_id') && Mage::getStoreConfig('admin/su/enable') && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getData('entity_id'), $this->getStatus());
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));

            if (($role->canEditOwnProductsOnly() && !$role->isOwnProduct($product)) ||
                !$role->isAllowedToEditProduct($product))
            {
                Mage::throwException(
                    Mage::helper('aitpermissions')->__(
                        'Sorry, you have no permissions to delete this product. For more details please contact site administrator.'
                    )
                );
            }
        }

        return $this;
    }

    /**
     * @refactor
     * check if following bug reproduces when commented
     * 0027984: Admin (allowed manage own products only) can manage all products by direct URL.
     */

//    protected function _afterLoad()
//    {
//        parent::_afterLoad();
//        $controller = Mage::app()->getRequest()->getControllerName();
//        if (Mage::helper('aitpermissions')->isPermissionsEnabled() &&
//            Mage::helper('aitpermissions/access')->isAllowManageEntity('product') &&
//            Mage::app()->getStore()->isAdmin() &&
//            ($this->getCreatedBy() !== Mage::getSingleton('admin/session')->getUser()->getUserId()) &&
//            (!in_array($controller, array('sales_order_edit', 'sales_order_create'))))
//        {
//            $this->unsetData();
//        }
//
//        return $this;
//    }
} } 