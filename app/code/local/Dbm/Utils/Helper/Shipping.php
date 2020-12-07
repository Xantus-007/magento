<?php

class Dbm_Utils_Helper_Shipping extends Mage_Core_Helper_Abstract
{
    const SHIPPING_CODE_COLISSIMO = 'colissimo';
    const SHIPPING_CODE_CHRONOPOST = 'chronopost';
    
    public function getDiffData()
    {
        $result = array();
        $cHelper = Mage::helper('checkout/cart');
        $cart = $cHelper->getQuote()->getData();
        $amount = '';
        $diff = '';
        $ruleId = Mage::getStoreConfig('shipping/inside_site_shipping/rule_id');
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        
        if($rule->getId() == $ruleId && $rule->getIsActive())
        {
            $conditions = unserialize($rule->getConditionsSerialized());
            $max = $conditions['conditions'][0]['value'];

            if(!isset($cart['grand_total']))
            {
                $amount = '';
            }
            else
            {
                $amount = $cart['grand_total'];
            }
            
            $result = array(
                'max' => $max,
                'amount' => $amount
            );
        }
        
        return $result;
    }
    
    public function getDiffFormattedPrice()
    {
        $diffData = $this->getDiffData();
        $result = null;
        
        if(isset($diffData['max']) && isset($diffData['amount']))
        {
            $result = str_replace('.', ',', sprintf('%0.2f', $diffData['max'] - $diffData['amount']));
        }
        
        return $result;
    }
    
    public function shouldDisplayMessage()
    {
        $data = $this->getDiffData();
        return $data['amount'] <= $data['max'];
    }

    /**
     * Returns OWEBIA shipping number.
     */
    public function getShippingCode(Mage_Sales_Model_Order $order, $trackNumber)
    {
        $result = $trackNumber;
        $code = $this->_getShippingCode($order);

        if($code)
        {
            $result = $code.':'.$result;
        }

        return $result;
    }

    public function getShippingTitle(Mage_Sales_Model_Order $order)
    {
        return ucwords($this->_getShippingCode($order));
    }

    protected function _getShippingCode(Mage_Sales_Model_Order $order)
    {
        $result = null;
        $preg = '#'.self::SHIPPING_CODE_COLISSIMO.'#i';

        if(preg_match($preg, $order->getShippingMethod()))
        {
            $result = self::SHIPPING_CODE_COLISSIMO;
        }
        else
        {
            $result = self::SHIPPING_CODE_CHRONOPOST;
        }

        return $result;
    }
}