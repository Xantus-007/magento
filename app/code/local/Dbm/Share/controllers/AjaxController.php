<?php

class Dbm_Share_AjaxController extends Dbm_Share_Controller_Auth 
{
    protected function _getPublicActions()
    {
        return array();
    }

    public function likeAction()
    {
        $result = array(
            'isValid' => true,
            'message' => '',
            'action' => 0,
            'newCount' => 0
        );
        $params = $this->getRequest()->getParams();
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();

        if($this->getRequest()->isXmlHttpRequest() && $customer->getId())
        {
            $element = Mage::getModel('dbm_share/element')->load($params['id']);

            if($element->getId())
            {
                $isLiked  = $element->isLikedBy($customer);
                $isLiked ? $element->unlike($customer) : $element->like($customer);

                $result['action'] = intval(!$isLiked);
                $result['newCount'] = $element->getLikeCount();
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function getreceipttranslateformAction(){
        $magento_block = Mage::getSingleton('core/layout');
        $result['ok'] = false;
        
        $locale = $this->getRequest()->getParam('locale');
        Mage::log('handle => ' . $locale);
        $block = $magento_block
                    ->createBlock('core/template')
                    ->setTemplate('dbm/share/form/ajax/translate.phtml')
                    ->setData('locale', $locale)
                ;
        $result['html'] = $block->toHtml();
        $result['ok'] = true;
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}