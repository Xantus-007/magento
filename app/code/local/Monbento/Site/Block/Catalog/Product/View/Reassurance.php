<?php

class Monbento_Site_Block_Catalog_Product_View_Reassurance extends Mage_Core_Block_Template 
{
    public function getBlocs()
    {
        $_product = Mage::registry('current_product');

        $blocsTmp = array();
        $blocs = array();
        
        $ids = array();
        for($i=1;$i<4;$i++)
        {
            if($id = $_product->getData('select_reassurance_'.$i)) $ids[] = $id;
        }

        $collectionBlocs = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $ids))
                    ->addAttributeToFilter('status', array('eq' => 1));
        
        foreach($collectionBlocs as $post)
        {

            $blocsTmp[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($post->getEntityId());
        }

        $blocs = $blocsTmp;
        foreach ($blocsTmp as $key => $bloc) 
        {
            if ($bloc->getData('url_key') == 'made-in-france'){
                unset($blocs[$key]);
                array_unshift($blocs, $bloc);
            }
        }

        return $blocs;
    }
    
}