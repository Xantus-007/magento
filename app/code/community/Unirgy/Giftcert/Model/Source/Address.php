<?php

class Unirgy_Giftcert_Model_Source_Address
{
    const CUSTOM   = 0;
    const BILLING  = 1;
    const SHIPPING = 2;
    protected static $options = array(
        self::CUSTOM   => "Use customer entry",
        self::BILLING  => "Use billing address",
        self::SHIPPING => "Use shipping address",
    );

    public function toOptionArray()
    {
        $options = array();
        $hlp = Mage::helper("ugiftcert");
        foreach (self::$options as $k => $lbl) {
            $options[] = array(
                'value' => $k,
                'label' => $hlp->__($lbl)
            );
        }
        return $options;
    }

    public static function getOptionsStatic()
    {
        $options = array();
        $hlp     = Mage::helper("ugiftcert");
        foreach (self::$options as $k => $lbl) {
            $options[$k] = $hlp->__($lbl);
        }
        return $options;
    }
}