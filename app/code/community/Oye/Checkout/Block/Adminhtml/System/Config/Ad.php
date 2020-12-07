<?php

class Oye_Checkout_Block_Adminhtml_System_Config_Ad extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
//        parent::_construct();
        $this->setTemplate('oye/ad.phtml');
    }

    /**
     * Return element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
}