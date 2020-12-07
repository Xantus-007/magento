<?php

class Monbento_Site_Model_Rewrite_Checkout_Type_Onepage extends Spletnisistemi_Checkoutnewsletter_Model_Checkout_Type_Onepage
{
    /**
     * Check if fiscal code is filled in ITA store
     * @param array $data
     * @param int $customerAddressId
     * @return array|Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $address = Mage::getModel('customer/address')->load($customerAddressId);

        if ($customer &&
            $address
        ) {
            if ($address->getData('country_id') == 'IT' && (
                    !$this->validateFiscalId($data['fiscal_id']) ||
                    !$this->validateFiscalId($customer->getFiscalId())
                )
            ) {
                $attributeLabel = $this->getFiscalIdAttrLabel();
                return array(
                    'error' => -1,
                    'message' => Mage::helper('eav')->__('"%s" is a required value.', Mage::helper('eav')->__($attributeLabel)));
            }
        }

        if (isset($data['country_id']) &&
            $data['country_id'] == 'IT'
        ) {
            if ($this->validateFiscalId($data['fiscal_id'])) {
                Mage::getSingleton('checkout/session')->setData('fiscal_id', $data['fiscal_id']);
                $this->getQuote()->getCustomer()->setData('fiscal_id', $data['fiscal_id']);
            } else {
                $attributeLabel = $this->getFiscalIdAttrLabel();
                return array(
                    'error' => -1,
                    'message' => Mage::helper('eav')->__('"%s" is a required value.', Mage::helper('eav')->__($attributeLabel)));
            }
        }
        return parent::saveBilling($data, $customerAddressId);
    }

    /**
     * Validate fiscal id.
     * @param $fiscalId
     * @return bool
     */
    protected function validateFiscalId($fiscalId)
    {
        $fiscalId = trim($fiscalId);
        if (empty($fiscalId) ||
            !preg_match('/^[a-zA-Z-]{6}[0-9]{2}[a-zA-Z-][0-9]{2}[a-zA-Z-][0-9]{3}[a-zA-Z-]$/', $fiscalId)) {
            return false;
        }
        return true;
    }

    /**
     * Get fiscal_id attribute label.
     * @return string
     */
    protected function getFiscalIdAttrLabel()
    {
        return $this->getQuote()
            ->getCustomer()
            ->getResource()
            ->getAttribute('fiscal_id')
            ->getFrontendLabel();
    }
}
