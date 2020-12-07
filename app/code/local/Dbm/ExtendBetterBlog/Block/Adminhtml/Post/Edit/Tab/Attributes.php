<?php

class Dbm_ExtendBetterBlog_Block_Adminhtml_Post_Edit_Tab_Attributes extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Attributes
{
    /**
     * prepare the attributes for the form
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     * @author Sam
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('mageplaza_betterblog')->__('Post Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        $setId = $this->getRequest()->getParam('set', null);
        if(Mage::registry('current_post')->getId())
        {
            $setId = Mage::registry('current_post')->getAttributeSetId();
            $attributes->getSelect()
                    ->joinLeft(array("attribute_set" => Mage::getSingleton('core/resource')->getTableName("eav_entity_attribute")), "main_table.attribute_id = attribute_set.attribute_id", array("attribute_set_id" => "attribute_set.attribute_set_id"))
                    ->where('attribute_set.attribute_set_id = ?', $setId);
        }
        elseif($setId)
        {
            $attributes->getSelect()
                    ->joinLeft(array("attribute_set" => Mage::getSingleton('core/resource')->getTableName("eav_entity_attribute")), "main_table.attribute_id = attribute_set.attribute_id", array("attribute_set_id" => "attribute_set.attribute_set_id"))
                    ->where('attribute_set.attribute_set_id = ?', $setId);
        }

        $attributes->getSelect()->reset( Zend_Db_Select::ORDER )->order('attribute_set.sort_order ASC');

        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('mageplaza_betterblog/post'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_post')->getData();
        if (!Mage::registry('current_post')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
            if($setId) {
                $formValues['attribute_set_id'] = $setId;
            }
        }
        $form->addValues($formValues);
        $form->setFieldNameSuffix('post');
        $this->setForm($form);
    }

}
