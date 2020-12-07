<?php

class Dbm_Cartpromo_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getPopupBackground()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $productsToExclude = explode(';', Mage::getStoreConfig('dbm_cart_popup/general/exclude_ids'));

        $show = true;
        foreach($quote->getAllVisibleItems() as $_item)
        {
            if(in_array($_item->getProductId(), $productsToExclude))
            {
                $show = false;
                break;
            }
        }

        $file = Mage::getStoreConfig('dbm_cart_popup/general/background');
        
        if($file != '' && $show){
            return '/media/dbm_cart/popup/' . $file;
        }else{
            return false;
        }
    }
}