<?php

class Dbm_Share_Helper_Payment extends Mage_Core_Helper_Abstract
{
    public function getPaymentNamesFromCodes()
    {
        return array(
            'atos_standard' => 'CB',
            'paypal_standard' => 'Paypal',
            'checkmo' => 'Cheque',
            'free' => 'Gratuit',
            'vads' => 'CB',
            'paypal_express' => 'Paypal Express'
        );
    }
}