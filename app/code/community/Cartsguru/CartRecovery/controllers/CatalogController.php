<?php

class Cartsguru_CartRecovery_CatalogController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $params = $this->getRequest()->getParams();
        $auth_key = $helper->getAuthKey();
        // Stop if not authenticated
        if (!isset($params['cartsguru_auth_key']) || $auth_key !== $params['cartsguru_auth_key'] || $params['cartsguru_auth_key'] == '') {
            return;
        }
        // Get input values
        $offset = isset($params['cartsguru_catalog_offset']) ? $params['cartsguru_catalog_offset'] : 0;
        $limit = isset($params['cartsguru_catalog_limit']) ? $params['cartsguru_catalog_limit'] : 50;

        $store = Mage::app()->getStore();
        $catalog = Mage::getModel('cartsguru_cartrecovery/catalog');

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($catalog->generateFeed($store, $offset, $limit)));
    }
}
