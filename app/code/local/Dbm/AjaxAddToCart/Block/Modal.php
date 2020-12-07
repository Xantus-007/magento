<?php

class Dbm_AjaxAddToCart_Block_Modal extends Mage_Core_Block_Template
{
    
    public function getFrancoMsg()
    {
        if(Mage::getStoreConfig('dbm_modal/defaultmodal/showfranco'))
        {
            $helper = Mage::helper('dbm_utils/shipping');

            if($helper->shouldDisplayMessage('allItems') === true) {
                $amountToFranco = $helper->getDiffFormattedPrice('allItems');
                return Mage::helper('core')->formatPrice($amountToFranco, true);
            }
        }

        return null;
    }

}