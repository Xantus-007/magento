<?php

class Dbm_Utils_Model_Observer
{
    const LINK_TOKEN_CONFIG_PATH = 'dbm_utils/link_cache/token';
    
    public function afterCacheHandler()
    {
        $config = Mage::getModel('core/config');
        $config->saveConfig(self::LINK_TOKEN_CONFIG_PATH, md5(time()));
    }
    
    public function mobileHomePage()
    {
        if(preg_match("/Android|iPhone|iPad|iPod/i", $_SERVER['HTTP_USER_AGENT']) and strlen($_SERVER['REQUEST_URI']) <= 1 and !strpos($_SERVER['REQUEST_URI'], '/blog/')) {
            $shopUrl = Mage::getUrl('shop');
            header('Location: '.$shopUrl);
            exit();
        }
    }
    
    public function cancelCouponFix($observer)
    {
        $event = $observer->getEvent();
        $order = $event->getPayment()->getOrder();
        if ($order->canCancel())
        {
            if ($code = $order->getCouponCode())
            {
                $coupon = Mage::getModel('salesrule/coupon')->load($code, 'code');
                $couponUsed = $coupon->getTimesUsed();
                $coupon->setTimesUsed($couponUsed-1);
                $coupon->save();
                
                if ($customerId = $order->getCustomerId()) {
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    $ruleCustomer->loadByCustomerRule($customerId, $coupon->getRuleId());
                    if ($ruleCustomer->getId()) {
                        $customerCouponUsed = $ruleCustomer->getTimesUsed();
                        $ruleCustomer->setTimesUsed($customerCouponUsed-1);
                        $ruleCustomer->save();
                    }
                }
            }
            //below is added by franky
            if ($rules = $order->getAppliedRuleIds()) 
            {
                foreach(explode(",", $rules) as $rule_id)
                {
                    $rule = Mage::getModel('salesrule/rule')->load($rule_id);
                    $ruleUsed = $rule->getTimesUsed();
                    $rule->setTimesUsed($ruleUsed-1);
                    $rule->save();
                    
                    if ($customerId = $order->getCustomerId()) {
                        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                        $ruleCustomer->loadByCustomerRule($customerId, $rule_id);
                        if ($ruleCustomer->getId()) {
                            $customerCouponUsed = $ruleCustomer->getTimesUsed();
                            $ruleCustomer->setTimesUsed($customerCouponUsed-1);
                            $ruleCustomer->save();
                        }
                    }
                }
            }
        }
    }
}