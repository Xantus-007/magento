<?php 

class Dbm_Share_Block_List_Header_Best extends Dbm_Share_Block_Detail_Abstract 
{
    public function getElement()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection();
        $collection->addAll()
            ->orderByLikes()
        ;
        
        $catId = Mage::app()->getRequest()->getParam('category');
        
        if($catId > 0)
        {
            $catFilter = Mage::getModel('dbm_share/category')->load($catId);
            
            if($catFilter->getId())
            {
                $collection->addCategoryFilter($catFilter);
            }
        }
        
        if($this->getElementType())
        {
            $collection->addTypeFilter($this->getElementType());

            $item = $collection->getFirstItem();
        }

        return $collection->getFirstItem();
    }
}