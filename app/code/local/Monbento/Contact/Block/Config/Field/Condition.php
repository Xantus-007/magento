<?php

class Monbento_Contact_Block_Config_Field_Condition
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
            '1' => Mage::helper('monbentocontact')->__('Order Number'),
            '2' => Mage::helper('monbentocontact')->__('Photo + Order Number + Invoice'),
            '3' => Mage::helper('monbentocontact')->__('Company + City + Country'),
            '4' => Mage::helper('monbentocontact')->__('Country + Name of the media'),
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
    public function setInputName($value)
    {
        return $this->setName($value);
    }

}
