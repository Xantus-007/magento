<?php

class Monbento_Contact_Block_Config_Field_Category
    extends Mage_Core_Block_Html_Select
{

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() {

        $options = array(
            '' => '',
            'order' => Mage::helper('monbentocontact')->__('My order'),
            'pro' => Mage::helper('monbentocontact')->__('Professionals'),
            'product' => Mage::helper('monbentocontact')->__('Products'),
            'other' => Mage::helper('monbentocontact')->__('Other'),
        );

        foreach ($options as $k => $option) {
            $this->addOption($k, $option);
        }

        return parent::_toHtml();
    }

    /**
     * Set input name
     *
     * @param sting $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value);
    }
}
