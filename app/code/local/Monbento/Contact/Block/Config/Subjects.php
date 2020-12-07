<?php

class Monbento_Contact_Block_Config_Subjects
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var Monbento_Contact_Block_Config_Field_Category
     */
    protected $_itemRendererCategory;

    /**
     * @var Monbento_Contact_Block_Config_Field_Condition
     */
    protected $_itemRendererCondition;

    /**
     * Prepare to render
     */
    public function _prepareToRender()
    {
        $this->addColumn('category', array(
            'label' => Mage::helper('monbentocontact')->__('Category'),
            'renderer' => $this->_getRendererCategory(),
        ));

        $this->addColumn('subject', array(
            'label' => Mage::helper('monbentocontact')->__('Subject'),
            'style' => 'width:200px',
        ));
        $this->addColumn('email', array(
            'label' => Mage::helper('monbentocontact')->__('Address mail'),
            'style' => 'width:100px',
        ));
        $this->addColumn('condition', array(
            'label' => Mage::helper('monbentocontact')->__('Condition'),
            'renderer' => $this->_getRendererCondition(),
            'setExtraParams' => 'style="width: 64px;"',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('monbentocontact')->__('Add');
    }

    /**
     * Retrieve category column renderer
     *
     * @return Monbento_Contact_Block_Config_Field_Category
     */
    protected function _getRendererCategory()
    {
        if (!$this->_itemRendererCategory) {
            $this->_itemRendererCategory = $this->getLayout()->createBlock(
                'monbentocontact/config_field_category',
                '',
                array('is_render_to_js_template' => true)
            )->setExtraParams('style="width: 84px;"');
        }
        return $this->_itemRendererCategory;
    }

    /**
     * Retrieve condition column renderer
     *
     * @return Monbento_Contact_Block_Config_Field_Condition
     */
    protected function _getRendererCondition()
    {
        if (!$this->_itemRendererCondition) {
            $this->_itemRendererCondition = $this->getLayout()->createBlock(
                'monbentocontact/config_field_condition',
                '',
                array('is_render_to_js_template' => true)
            )->setExtraParams('style="width: 84px;"');
        }
        return $this->_itemRendererCondition;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRendererCategory()->calcOptionHash($row->getData('category')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getRendererCondition()->calcOptionHash($row->getData('condition')),
            'selected="selected"'
        );
    }
}
