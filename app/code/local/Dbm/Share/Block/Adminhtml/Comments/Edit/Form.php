<?php

class Dbm_Share_Block_Adminhtml_Comments_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    function _prepareForm()
    {
        $comModel = Mage::getModel('dbm_share/comment');
        $params = $this->getRequest()->getParams();
        $shareHelper = Mage::helper('dbm_share');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldSet('misc', array(
            'legend' => 'Intitulés du commentaire'
        ));
        
        $fieldset->addField('id', 'hidden', array(
            'name' => 'id'
        ));

        $fieldset->addField('message', 'textarea', array(
            'label' => 'Message',
            'name' => 'message'
        ));

        $fieldset->addField('id_element', 'text', array(
            'label' => 'Element ID',
            'name' => 'id_element',
            'disabled' => true,
            'readonly' => true,
        ));
        
        $fieldset->addField('status', 'text', array(
            'label' => 'Status ID',
            'name' => 'status_id',
        ));
        
        $fieldset->addField('created_at', 'date', array(
            'label'     => 'Date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        
        if(isset($params['id']) && $comModel->load($params['id']))
        {
            $form->setValues($comModel);

        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}