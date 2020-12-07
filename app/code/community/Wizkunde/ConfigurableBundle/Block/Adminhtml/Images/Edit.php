<?php

class Wizkunde_ConfigurableBundle_Block_Adminhtml_Images_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'configurablebundle';
        $this->_controller = 'adminhtml_images';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Composite Image'));
        $this->_updateButton('delete', 'label', $this->__('Delete Composite Image'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('configurablebundle')->getId()) {
            return $this->__('Edit Composite Image');
        }
        else {
            return $this->__('New Composite Image');
        }
    }
}