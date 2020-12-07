<?php

class D3_Newsladdressimport_Block_Adminhtml_Newsladdressimport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('newsladdressimport_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('newsladdressimport')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('newsladdressimport')->__('Item Information'),
          'title'     => Mage::helper('newsladdressimport')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('newsladdressimport/adminhtml_newsladdressimport_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}