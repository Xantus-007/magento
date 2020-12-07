<?php

class Monbento_Site_Model_Adminhtml_Source_Selecttypodefault extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const SOFIA_PRO_REGULAR = 1;
    const SOFIA_PRO_BOLD = 2;
    const SOFIA_PRO_BOLD_CONDENSED = 3;
    const SOFIA_PRO_LIGHT = 4;
    const SOFIA_PRO_EXTA_LIGHT = 5;

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
                'label' => Mage::helper('core')->__('Sofia Pro Regular'),
                'value' => self::SOFIA_PRO_REGULAR
            ),
            array(
                'label' => Mage::helper('core')->__('Sofia Pro Bold'),
                'value' => self::SOFIA_PRO_BOLD
            ),
            array(
                'label' => Mage::helper('core')->__('Sofia Pro Bold Condensed'),
                'value' => self::SOFIA_PRO_BOLD_CONDENSED
            ),
            array(
                'label' => Mage::helper('core')->__('Sofia Pro Light'),
                'value' => self::SOFIA_PRO_EXTA_LIGHT
            ),
            array(
                'label' => Mage::helper('core')->__('Sofia Pro ExtraLight'),
                'value' => self::SOFIA_PRO_EXTA_LIGHT
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
