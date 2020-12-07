<?php

class LinkstureDCCL_ApplyCoupon_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
	$coupon_code = $this->getRequest()->getParam('code');
	$return_url=$this->getRequest()->getParam('return_url');
        $session = $this->_getSession();
        
	if ($coupon_code != '') 
	{
            Mage::getSingleton("checkout/session")->setData("coupon_code",$coupon_code);
            Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($coupon_code)->save();
            Mage::getSingleton('core/session')->addSuccess($this->__('Coupon was automatically applied'));
            
            $session->setIsPartnerCodeApplied(true);
	}
	else 
	{
            Mage::getSingleton("checkout/session")->setData("coupon_code","");
            $cart = Mage::getSingleton('checkout/cart');
            foreach(Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ) 
            {
		$cart->removeItem($item->getId());
            }
            $cart->save();
            
            $session->setIsPartnerCodeApplied(false);
	}
		 
	if($return_url=='')
	{
            $this->_redirect("/");
        }
	else
	{
            header("Location: ".$return_url);
            exit();
	}
    }
    
    protected function _getSession()
    {
        return Mage::getModel('dbm_customer/session');
    }

}