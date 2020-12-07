<?php 

class Mage_Adminhtml_Renderer_RemiseCoupon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $coupon = $row->getCouponCode();
        if($coupon != 'NULL' && !empty($coupon))
        {
            $oCoupon = Mage::getModel('salesrule/coupon')->load($coupon, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            
            return $oRule->getName();
        }
        
        return '';
    }
}