<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCmsBlockEditForm.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ chmgYMarUriIppyo('c209b3867428f2aa5efab15029a9ea67'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCmsBlockEditForm extends Mage_Adminhtml_Block_Cms_Block_Edit_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            // if page is not assigned to any store views but permitted, will allow to delete and disable it
            $blockModel = Mage::registry('cms_block');
            if ($blockModel->getStoreId() && is_array($blockModel->getStoreId()))
            {
                foreach ($blockModel->getStoreId() as $blockStoreId)
                {
                    if (!in_array($blockStoreId, $role->getAllowedStoreviewIds()))
                    {
                        $fieldset = $this->getForm()->getElement('base_fieldset');
                        $fieldset->removeField('is_active');
                        break 1;
                    }
                }
            }
        }
        
        return $this;
    }
} } 