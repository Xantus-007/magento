<?php
class D3_Newsladdressimport_Block_Adminhtml_Newsladdressimport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_newsladdressimport';
    $this->_blockGroup = 'newsladdressimport';
    $this->_headerText = Mage::helper('newsladdressimport')->__('Newsletter Address Import');
    $this->_addButtonLabel = Mage::helper('newsladdressimport')->__('Add Import Item');
    parent::__construct();
  }
}