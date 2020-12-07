<?php

class Monbento_Site_Block_Rewrite_Checkout_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    public function getCurrentSelectedAddress()
    {
        $addressId = $this->getAddress()->getCustomerAddressId();
        if (empty($addressId)) {
            if ($type=='billing') {
                $address = $this->getCustomer()->getPrimaryBillingAddress();
            }
            if ($address) {
                $addressId = $address->getId();
            }
        }
        
        if(!empty($addressId))
        {
            $address = Mage::getModel('customer/address')->load($addressId);
            return $address->format('oneline');
        }
        else
        {
            return false;
        }
    }
}
