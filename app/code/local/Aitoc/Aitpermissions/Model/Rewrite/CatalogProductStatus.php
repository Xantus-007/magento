<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/CatalogProductStatus.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ToBjSeEmQmqRwCgh('ca57d00eb93f7360d690b0d9dd89fcd7'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus extends Mage_Catalog_Model_Product_Status
{
    const STATUS_AWAITING   = 3;

    public static function getOptionArray()
    {
        $options = array(
            self::STATUS_ENABLED    => Mage::helper('catalog')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('catalog')->__('Disabled'),
        );

        if (Mage::getStoreConfig('admin/su/enable'))
        {
             $options[self::STATUS_AWAITING]  = Mage::helper('catalog')->__('Awaiting approve');
        }

        return $options;
    }

    public static function getAllOptions()
    {
        $options = array();

        $permissionsEnabled = Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled();
        $suEnabled = Mage::getStoreConfig('admin/su/enable');

        if (($permissionsEnabled && $suEnabled) && (self::_isProductNew() || self::_isProductNotApproved()))
        {
           $options[] = array(
                'value' => self::STATUS_AWAITING,
                'label' => Mage::helper('catalog')->__('Awaiting approve')
            );
        }
        else
        {
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('catalog')->__('-- Please Select --')
            );
            
            foreach (self::getOptionArray() as $index => $value)
            {
                $options[] = array(
                    'value' => $index,
                    'label' => $value
                );
            }
            
            unset($options[self::STATUS_AWAITING]);
        }
        
        return $options;
    }

    private static function _isProductNew()
    {
        $request = Mage::app()->getRequest();

        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        return $controllerName == 'catalog_product' &&
            $actionName == 'new';
    }

    private static function _isProductNotApproved()
    {
        $request = Mage::app()->getRequest();

        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        return $controllerName == 'catalog_product' &&
            $actionName == 'edit' &&
            $request->getParam('id') &&
            !self::_isApproved($request->getParam('id'));
    }

    private static function _isApproved($id)
    {
        return Mage::getModel('aitpermissions/approve')->isApproved($id);
    }
} } 