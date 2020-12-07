<?php

class Oye_Checkout_Model_Source_Type
{

    public function toOptionArray()
    {
        return array(
            array('value' => Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_STANDARD, 'label'=>Mage::helper('oyecheckout')->__('Standard vertical steps')),
            array('value' => Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_HORIZONTAL, 'label'=>Mage::helper('oyecheckout')->__('Horizontal steps')),
            array('value' => Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_ONE_STEP, 'label'=>Mage::helper('oyecheckout')->__('One Step Chekout')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_STANDARD => Mage::helper('oyecheckout')->__('Standard vertical steps'),
            Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_HORIZONTAL => Mage::helper('oyecheckout')->__('Horizontal steps'),
            Oye_Checkout_Helper_Data::CHECKOUT_LAYOUT_ONE_STEP => Mage::helper('oyecheckout')->__('One Step Chekout'),
        );
    }

}
