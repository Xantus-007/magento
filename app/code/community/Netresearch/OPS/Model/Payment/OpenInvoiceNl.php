<?php
/**
 * Netresearch_OPS_Model_Payment_OpenInvoiceNl
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Model_Payment_OpenInvoiceNl
    extends Netresearch_OPS_Model_Payment_OpenInvoice_Abstract
{
    const CODE = 'Open Invoice NL';

    /** if we can capture directly from the backend */
    protected $_canBackendDirectCapture = false;

    protected $_canCapturePartial = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canUseInternal = true;

    /** info source path */
    protected $_infoBlockType = 'ops/info_redirect';

    /** payment code */
    protected $_code = 'ops_openInvoiceNl';

    /** ops payment code */
    protected function getOpsCode()
    {
        return self::CODE;
    }

    /**
     * Open Invoice NL is not available if quote has a coupon
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote=null)
    {
        /* availability depends on quote */
        if (false == $quote instanceof Mage_Sales_Model_Quote) {
            return false;
        }

        /* not available if quote contains a coupon */
        if ($quote->getSubtotal() != $quote->getSubtotalWithDiscount()) {
            return false;
        }

        /* not available if there is no gender or no birthday */
        if (is_null($quote->getCustomerGender()) || is_null($quote->getCustomerDob())) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * get some method dependend form fields 
     *
     * @param Mage_Sales_Model_Quote $order 
     * @return array
     */
    public function getMethodDependendFormFields($order, $requestParams=null)
    {
        $billingAddress  = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $street = str_replace("\n", ' ',$shippingAddress->getStreet(-1));
        $regexp = '/^([^0-9]*)([0-9].*)$/';
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);
        $formFields['OWNERADDRESS']                     = trim($splittedStreet[1]);
        $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = trim($splittedStreet[2]);
        $formFields['OWNERZIP']                         = $shippingAddress->getPostcode();
        $formFields['OWNERTOWN']                        = $shippingAddress->getCity();
        $formFields['OWNERCTY']                         = $shippingAddress->getCountry();
        $formFields['OWNERTELNO']                       = $shippingAddress->getTelephone();
        $formFields['CUID']                             = '';

        $street = str_replace("\n", ' ',$billingAddress->getStreet(-1));
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }
        $formFields['ECOM_SHIPTO_POSTAL_NAME_PREFIX']   = $billingAddress->getPrefix();
        $formFields['ECOM_SHIPTO_POSTAL_NAME_FIRST']    = $billingAddress->getFirstname();
        $formFields['ECOM_SHIPTO_POSTAL_NAME_LAST']     = $billingAddress->getLastname();
        $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1']  = trim($splittedStreet[1]);
        $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = trim($splittedStreet[2]);
        $formFields['ECOM_SHIPTO_POSTAL_POSTALCODE']    = $billingAddress->getPostcode();
        $formFields['ECOM_SHIPTO_POSTAL_CITY']          = $billingAddress->getCity();
        $formFields['ECOM_SHIPTO_POSTAL_COUNTRYCODE']   = $billingAddress->getCountry();

        // copy some already known values
        $formFields['ECOM_SHIPTO_ONLINE_EMAIL']         = $order->getCustomerEmail();

        if (is_array($requestParams)) {
            if (array_key_exists('OWNERADDRESS', $requestParams)) {
                $formFields['OWNERADDRESS'] = $requestParams['OWNERADDRESS'];
            }
            if (array_key_exists('ECOM_BILLTO_POSTAL_STREET_NUMBER', $requestParams)) {
                $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = $requestParams['ECOM_BILLTO_POSTAL_STREET_NUMBER'];
            }
            if (array_key_exists('CUID', $requestParams)) {
                $formFields['CUID'] = $requestParams['CUID'];
            }
            if (array_key_exists('ECOM_SHIPTO_POSTAL_STREET_LINE1', $requestParams)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $requestParams['ECOM_SHIPTO_POSTAL_STREET_LINE1'];
            }
            if (array_key_exists('ECOM_SHIPTO_POSTAL_STREET_NUMBER', $requestParams)) {
                $formFields['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = $requestParams['ECOM_SHIPTO_POSTAL_STREET_NUMBER'];
            }
        }

        // Order Details
        $count = 1;
        foreach($order->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $count++;
        }

        /* add shipping item */
        $formFields['ITEMID' . $count]      = 'SHIPPING';
        $formFields['ITEMNAME' . $count]    = substr($order->getShippingDescription(), 0, 30);
        $formFields['ITEMPRICE' . $count]   = number_format($order->getBaseShippingInclTax(), 2, '.', '');
        $formFields['ITEMQUANT' . $count]   = 1;
        $formFields['ITEMVATCODE' . $count] = str_replace(',', '.',(string)(float)$this->getShippingTaxRate($order)) . '%';
        $formFields['TAXINCLUDED' . $count] = 1;

        return $formFields;
    }

    protected function getShippingTaxRate($order)
    {
        $shippingProduct  = new Varien_Object();
        $priceIncludesTax = Mage::helper('tax')->priceIncludesTax($order->getStore());
        $taxPercent       = $shippingProduct->getTaxPercent();
        $taxClassId       = Mage::helper('tax')->getShippingTaxClass($order->getStore());
        $shippingProduct->setTaxClassId($taxClassId);
        if (is_null($taxPercent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')->getRateRequest($order->getShippingAddress(), $order->getBillingAddress(), null, $order->getStore());
                $taxPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $order->getStore());
            $taxPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($shippingProduct->getTaxClassId()));
        }

        return $taxPercent;
    }

    public function getItemFormFields($count, $item)
    {
        $formFields = parent::getItemFormFields($count, $item);

        /* use price including tax */
        $formFields['ITEMPRICE' . $count]   = number_format($item->getBasePriceInclTax(), 2, '.', '');
        /* indicate that price already includes tax */
        $formFields['TAXINCLUDED' . $count] = 1;

        return $formFields;
    }

    /**
     * get question for fields with disputable value
     * users are asked to correct the values before redirect to OPS
     * 
     * @param mixed $quote 
     * @return string|null
     */
    public function getQuestion($quote, $requestParams)
    {
        if (empty($requestParams)) {
            return Mage::helper('ops')->__('Please make sure that your street and house number are correct.')
                . '<br />'
                . Mage::helper('ops')->__('Please enter your social security number/commercial register number.');
        }
    }

    /**
     * get an array of fields with disputable value
     * users are asked to correct the values before redirect to OPS
     * 
     * @param Mage_Sales_Model_Order $quote 
     * @return array
     */
    public function getQuestionedFormFields($quote, $requestParams)
    {
        return empty($requestParams) ? array(
            'CUID',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER',
            'OWNERADDRESS',
        ) : array();
    }
}
