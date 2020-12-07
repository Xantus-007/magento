<?php

class Altiplano_Ngroups_Block_Adminhtml_Ngroups_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $this->setTemplate('ngroups/form.phtml');

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ngroups_form', array('legend'=>Mage::helper('ngroups')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('ngroups')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('visible', 'select', array(
          'label'     => Mage::helper('ngroups')->__('Visibility on Frontend'),
          'name'      => 'visible',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('ngroups')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('ngroups')->__('Disabled'),
              ),
          ),
      ));


      $fieldset->addField('emails', 'editor', array(
          'label'     => Mage::helper('ngroups')->__('Add Subscribers emails'),
          'class'     => '',
          'style'     => 'width:274px; height:500px;',
          'name'      => 'emails',
      ));

      
     
      if ( Mage::getSingleton('adminhtml/session')->getNgroupsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNgroupsData());
          Mage::getSingleton('adminhtml/session')->setNgroupsData(null);
      } elseif ( Mage::registry('ngroups_data') ) {
          $form->setValues(Mage::registry('ngroups_data')->getData());
      }
      return parent::_prepareForm();
  }
}