<?php

class Dbm_Share_Block_List_Popular extends Dbm_Share_Block_List_Abstract
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $catId = Mage::app()->getRequest()->getParam('category', null);
        
        if($catId)
        {
            if($catId != 'all')
            {
                $cat = Mage::getModel('dbm_share/category')->load($catId);

                if($cat->getId())
                {
                    $collection->addCategoryFilter($cat);
                    $collection->orderByDate();
                }
            }
        }
        else
        {
            $collection->orderByLikes();
        }

        return $collection;
    }
}