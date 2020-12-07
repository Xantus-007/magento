<?php

class Monbento_Site_Model_Adminhtml_Source_Selectsizedefault extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const SIZE_HUGE = 1;
    const SIZE_BIG = 2;
    const SIZE_NORMAL = 3;
    const SIZE_SMALL = 4;

    /**
     * get possible values
     *
     * @access public
     * @return array
     * @author Kévin
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => '',
                'value' => 0
            ),
            array(
                'label' => Mage::helper('core')->__('Très grande (3.9375rem)'),
                'value' => self::SIZE_HUGE
            ),
            array(
                'label' => Mage::helper('core')->__('Grande (2.8125rem)'),
                'value' => self::SIZE_BIG
            ),
            array(
                'label' => Mage::helper('core')->__('Moyenne (1.875rem)'),
                'value' => self::SIZE_NORMAL
            ),
            array(
                'label' => Mage::helper('core')->__('Petite (1.3125rem)'),
                'value' => self::SIZE_SMALL
            )
        );
    }

    /**
     * Get list of all available values
     *
     * @access public
     * @return array
     * @author Kévin
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}