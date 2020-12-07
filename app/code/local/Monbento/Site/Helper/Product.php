<?php

class Monbento_Site_Helper_Product extends Mage_Core_Helper_Abstract
{
    
    public function getProductEstimateArrivalDate()
    {
        if(Mage::app()->getLocale()->getLocaleCode() == 'fr_FR')
        {
            $date = new Zend_Date();
            $date->setTimezone('Europe/Paris');

            if($date->isLater('16:30', Zend_Date::TIME_SHORT))
            {
                $date->addDay(1); // L'envoi partira demain
            }

            if((int) $date->get(Zend_Date::WEEKDAY_8601) <= 3)
            { // du lundi au mercredi
                $date->addDay(2);
            } 
            elseif((int) $date->get(Zend_Date::WEEKDAY_8601) == 7)
            {
                $date->addDay(3);
            } 
            else
            {
                $date->addDay(4);
            }

            $jour = $date->toString(Zend_Date::WEEKDAY, 'fr_FR');
            return ucwords($jour);
        }
        else
        {
            return false;
        }
    }
    
    public function displaySelectSimilar()
    {
        if($product = Mage::registry('current_product'))
        {
            if(!$product->hasOptions() && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            {
                $categories = $product->getCategoryCollection()->addAttributeToSelect('select_colori_for_product');
                foreach($categories as $category)
                {
                    if($category->getSelectColoriForProduct()) return true;
                }
            }
        }
        return false;
    }
    
}
