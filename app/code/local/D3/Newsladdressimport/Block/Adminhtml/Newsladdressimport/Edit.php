<?php

class D3_Newsladdressimport_Block_Adminhtml_Newsladdressimport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'newsladdressimport';
        $this->_controller = 'adminhtml_newsladdressimport';
        
        $this->_updateButton('save', 'label', Mage::helper('newsladdressimport')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('newsladdressimport')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('newsladdressimport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'newsladdressimport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'newsladdressimport_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('newsladdressimport_data') && Mage::registry('newsladdressimport_data')->getId() ) {
            return Mage::helper('newsladdressimport')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('newsladdressimport_data')->getTitle()));
        } else {
            return Mage::helper('newsladdressimport')->__('Add Item');
        }
    }
}