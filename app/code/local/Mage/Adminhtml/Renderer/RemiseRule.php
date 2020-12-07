<?php 

class Mage_Adminhtml_Renderer_RemiseRule extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $ruleIds = $row->getAppliedRuleIds();
        if($ruleIds != 'NULL' && !empty($ruleIds))
        {
            $ruleIds = explode(',', $ruleIds);
            foreach($ruleIds as $ruleId)
            {
                $rule = Mage::getModel('salesrule/rule')->load($ruleId);
                if($coupon = $rule->getCouponCode())
                {
                    if(strtolower($coupon) == strtolower($row->getCouponCode())) continue;
                    
                    return $rule->getName();
                }
                else
                {
                    return $rule->getName();
                }
            }
        }
        
        return '';
    }
}