<?php

class Monbento_Site_Block_Rewrite_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    public function getBlocPromoForListing()
    {
        $blocs = array();
        $blocsCategoryCatId = Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_blocs_promo_cat_cat_id');
        
        $collectionBlocPromos = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->addCategoryFilter($blocsCategoryCatId);
        
        $collectionBlocPromos->getSelect()->order(new Zend_Db_Expr('RAND()'));
        
        foreach($collectionBlocPromos as $bloc)
        {
            $blocs[] = $bloc;
        }

        return $blocs;
    }
}
