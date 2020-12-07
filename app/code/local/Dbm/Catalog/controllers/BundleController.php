<?php

class Dbm_Catalog_BundleController extends Mage_Core_Controller_Front_Action
{
    public function generate_imageAction()
    {
        $params = $this->getRequest()->getParams();
        $v2 = Mage::getModel('dbm_catalog/api_v2');
        
        $optionIds = array();
        
        if(is_array($params['items']))
        {
            foreach($params['items'] as $itemData)
            {
                $tmpClass = new stdClass();
                
                $parts = explode('_', $itemData);
                $tmpClass->key = $parts[0];
                $tmpClass->value = $parts[1];
                
                $optionIds[] = $tmpClass;
            }
        }

        $url = $v2->makeBundleImage($params['store_view'], $params['product_id'], $optionIds);
        $ext = substr($url, strrpos($url, '.')+1);
        $mime = 'image/'.$ext;
        
        if(Mage::helper('dbm_share')->isMimeTypeAllowed($mime))
        {
            $this->getResponse()->setHeader('Content-Type', 'image/'.$ext);
            $contents = $contents = file_get_contents($url);
            $this->getResponse()->setBody($contents);
        }
        else
        {
            Mage::throwException(Mage::helper('dbm_share')->__('An error occured'));
        }
    }
}