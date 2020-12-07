<?php

class Dbm_Store_Block_Adminhtml_Unirgy_Storelocator_Form extends Unirgy_StoreLocator_Block_Adminhtml_Location_Edit_Tab_Form
{
    public function _prepareForm()
    {
        $result = parent::_prepareForm();
        $form = $this->getForm();
        
        $fieldset = $form->getElement('location_form');
        
        $fieldset->addField('type', 'select', array(
            'name' => 'type',
            'label'=> 'Type',
            'values' => array(
                array('label' => 'Store', 'value' => Dbm_Store_Model_Type::TYPE_STORE),
                array('label' => 'Spot', 'value' => Dbm_Store_Model_Type::TYPE_SPOT)
            )
        ));
        
        if (Mage::registry('location_data')) {
            $form->setValues(Mage::registry('location_data')->getData());
        }
        
        return $result;
    }
}