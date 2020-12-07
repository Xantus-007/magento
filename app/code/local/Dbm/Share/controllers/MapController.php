<?php

class Dbm_Share_MapController extends Dbm_Map_Controller_Abstract
{
    protected function _getPublicActions() {
        return array();
    }
    
    public function search_elementAction()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection();
        $result = $this->_search($collection);
        $this->_json($result);
    }

    public function search_my_elementsAction()
    {
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $collection = Mage::getModel('dbm_share/element')->getCollection();
        $collection->addCustomerFilter($customer);

        $result = $this->_search($collection);
        $this->_json($result);
    }
    
    protected function _search($collection)
    {
        $result = array();
        $type = $this->getRequest()->getParam('type', null);
        $filter = $this->getRequest()->getParam('filter', null);
        
        $helper = Mage::helper('dbm_share');
        $sizes = Mage::helper('dbm_share/image')->getSizes();
        $options = Mage::helper('dbm_share/image')->getOptionsForList();
        
        if($this->_bounds /*&& $this->getRequest()->isXmlHttpRequest()*/)
        {
            $collection->addBoundsFilter($this->_bounds);
            
            if($filter)
            {
                $collection->search($filter, Dbm_Share_Model_Element::TYPE_PHOTO);
            }
            
            foreach($collection as $element)
            {
                $result[] = array(
                    'id' => $element->getId(),
                    'title' => $element->getTitle(),
                    'description' => $element->getDescription(),
                    'lat' => $element->getLat(),
                    'lng' => $element->getLng(),
                    'thumb' =>  Mage::helper('dbm_share/image')->getElementImageUrl($element, $sizes['map_thumb'], $options),
                    'url' => Mage::getUrl('club/index/detail/id/'.$element->getId())
                );
            }
        }
        
        return $result;
    }
    
    protected function _json($result)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}