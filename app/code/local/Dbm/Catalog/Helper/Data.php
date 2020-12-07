<?php

class Dbm_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CUSTOM_BENTO_CAT_01 = 70;
    
    public function getShippingEstimate()
    {
        $result = null;
        
        if(Mage::app()->getStore()->getId())
        {
            $date = new Zend_Date();
            $date->setTimezone('Europe/Paris');

            if ($date->isLater('16:30', Zend_Date::TIME_SHORT)) {
                            $date->addDay(1); // L'envoi partira demain
            }

            if ((int) $date->get(Zend_Date::WEEKDAY_8601) <= 3) { // du lundi au mercredi
                    $date->addDay(2);
            }
            elseif ((int) $date->get(Zend_Date::WEEKDAY_8601) == 7) {
              $date->addDay(3);
            }
            else {
                    $date->addDay(4);
            }

            $result = $date->toString('yyyy-MM-dd HH:mm:ss');
        }
        
        return $result;
    }
    
    public function getPopupBackground(){
        $store = Mage::app()->getStore()->getCode();

        $file = Mage::getStoreConfig('dbm_catalog_popup/dbm_catalog_popup_' . $store . '/dbm_catalog_popup_' . $store . '_background');
        
        if($file != ''){
            return '/media/dbm_catalog/popup/' . $file;
        }else{
            return false;
        }
    }
}