<?php

class Dbm_Share_Block_List_Abstract extends Mage_Core_Block_Template
{
    public function getLoadedCollection()
    {
        return (!is_null($this->_getCollection())) ? $this->_getCollection()->load() : null;
    }

    protected function _getCollection()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection();
        $pageSize = Mage::app()->getRequest()->getParam('limit', null);
        $pageSize = $pageSize ? $pageSize : Dbm_Share_Helper_Data::API_LIST_PAGE_SIZE;
        $curPage = Mage::app()->getRequest()->getParam('p', null);
        
        $q = Mage::app()->getRequest()->getParam('q', null);
        $languagesFilter = Mage::app()->getRequest()->getParam('language', array());
        $allowedLocales = Mage::helper('dbm_share')->getAllowedLocalesWithoutExcludeLocales();
        
        $collection->addAll()
            ->setPageSize($pageSize)
            ->orderByDate()
        ;

        $isLanguagesEmpty = true;
        if(is_array($languagesFilter))
        {
            foreach($languagesFilter as $languageFilterItem)
            {
                if(strlen($languageFilterItem))
                {
                    $isLanguagesEmpty = false;
                }
            }
        }
        
        if($curPage)
        {
            $collection->setCurPage($curPage);
        }
        
        $customer = $this->getCustomer();
        if($customer && $customer->getId())
        {
            $collection->addCustomerLikes($customer);
        }

        if($this->getElementType())
        {
            $collection->addTypeFilter($this->getElementType());
        }

        if($q)
        {
            $collection->addLocaleFilter($languagesFilter);
            $collection->search($q, $this->getElementType());
        }

        if(count($languagesFilter))
        {
            if(!$isLanguagesEmpty)
            {
                $collection->addLocaleFilter($languagesFilter);
            }
        }

        return $collection;
    }

    public function getImageUrl(Dbm_Share_Model_Element $element, $size, $options = array())
    {
        return Mage::helper('dbm_share/image')->getElementImageUrl($element, $size, $options);
    }

    protected function _beforeToHtml() {
        parent::_beforeToHtml();

        $block = $this->_getToolbarBlock();
        if(!is_null($this->getLoadedCollection())) {
            $block->setLimit($this->getLoadedCollection()->getPageSize());
            $block->setCollection($this->getLoadedCollection());
        }
    }

    public function getCustomer()
    {
        return Mage::helper('dbm_customer')->getCurrentCustomer();
    }

    /*protected function _addQueryFilter($collection, $search)
    {
        
    }*/

    private function _getToolbarBlock()
    {
        return $this->getLayout()->getBlock('dbm_share.list_toolbar');
    }
}