<?php

/**
 * Grid block class
 *
 * Class Wizkunde_SoapSSO_Block_Adminhtml_Server
 */
class Wizkunde_ConfigurableBundle_Block_Adminhtml_Images extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor to define the block information
     */
    public function __construct()
    {
        $this->_blockGroup = 'configurablebundle';
        $this->_controller = 'adminhtml_images';
        $this->_headerText = $this->__('Composite Images');

        parent::__construct();
    }
}