<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Notification.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ RpeDYjPMNMhQicBw('7a30fa331967582df0a4100506f4b878'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Notification extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    
    public function send($product)
    {
        $suEmail = Mage::getStoreConfig('admin/su/email');

        if ('' == $suEmail)
        {
             $suEmail = Mage::getStoreConfig('trans_email/ident_sales/email');
        }

        $vars = (array)$this->_prepareVars($product);
        
        $name = 'Advanced Permissions Notification';
        $storeId = $product->getStoreId();
        
        Mage::getSingleton('core/translate')->setTranslateInline(false);
        
        $mailTemplate = Mage::getModel('core/email_template');
        $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $mailTemplate->setTemplateSubject($name);
        $emailId = Mage::getStoreConfig('admin/su/template', $storeId);

        $mailTemplate->sendTransactional(
            $emailId, 
            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $storeId),
            $suEmail,
            $name,
            $vars
        );

        Mage::getSingleton('core/translate')->setTranslateInline(true);

        return $mailTemplate->getSentSuccess();
    }
    
    protected function _prepareVars($product)
    {
        return array(
            'product_name' => $product->getName(),
            'product_sku' => $product->getSku(),
            'product_price' => $product->getPrice(),
            'admin_name' => Mage::getSingleton('admin/session')->getUser()->getName(),
            'website' => Mage::getBaseUrl(),
        );
    }   
} } 