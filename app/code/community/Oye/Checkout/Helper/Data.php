<?php

class Oye_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CHECKOUT_LAYOUT_STANDARD = 1;
    const CHECKOUT_LAYOUT_HORIZONTAL = 2;
    const CHECKOUT_LAYOUT_ONE_STEP = 3;


    public function isStandartLayout()
    {
        return Mage::getStoreConfig('oyecheckout/settings/type') == self::CHECKOUT_LAYOUT_STANDARD;
    }

    public function isHorisontalLayout()
    {
        return Mage::getStoreConfig('oyecheckout/settings/type') == self::CHECKOUT_LAYOUT_HORIZONTAL;
    }

    public function isOneStepLayout()
    {
        return Mage::getStoreConfig('oyecheckout/settings/type') == self::CHECKOUT_LAYOUT_ONE_STEP;
    }

    public function isResponsive()
    {
        return Mage::getStoreConfig('oyecheckout/settings/responsive');
    }

}
