<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminPermissionsButtons.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ hpkmYyDZcZhCiirw('198aec3cc1ae747cbefc9478282a051f'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminPermissionsButtons extends Mage_Adminhtml_Block_Permissions_Buttons
{
    protected function _prepareLayout()
    {
        $duplicateUrl = $this->getUrl(
            'aitpermissions/adminhtml_role/duplicate/',
            array('rid' => $this->getRequest()->getParam('rid'))
        );

        $onclick = 'window.location.href=\'' . $duplicateUrl . '\'';

        $duplicateButton = $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('adminhtml')->__('Duplicate Role'),
                'onclick' => $onclick,
                'class' => 'add'
            ));

        $this->setChild(
            'duplicateButton',
            $duplicateButton
        );
        
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('duplicateButton') . parent::getBackButtonHtml();
    }
    
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if (intval($this->getRequest()->getParam('rid')) == 0)
        {
            return;
        }
        
        return $this->getChildHtml('deleteButton');
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
} } 