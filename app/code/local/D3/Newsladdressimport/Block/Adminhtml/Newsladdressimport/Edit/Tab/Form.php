<?php

class D3_Newsladdressimport_Block_Adminhtml_Newsladdressimport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('newsladdressimport_form', array('legend'=>Mage::helper('newsladdressimport')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('newsladdressimport')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      /*$fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('newsladdressimport')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));*/
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('newsladdressimport')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('newsladdressimport')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('newsladdressimport')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('newsladdressimport')->__('Email Addresses (Comma seperated or with line breaks)'),
          'title'     => Mage::helper('newsladdressimport')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getNewsladdressimportData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsladdressimportData());
          Mage::getSingleton('adminhtml/session')->setNewsladdressimportData(null);
      } elseif ( Mage::registry('newsladdressimport_data') ) {
          $form->setValues(Mage::registry('newsladdressimport_data')->getData());
      }
      return parent::_prepareForm();
  }
}