<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ rRDROoRaiDomZRBr('110d86de454a96a93527231ab602c6f3');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitquantitymanager_Block_Rewrite_RssCatalogNotifyStock extends Mage_Rss_Block_Catalog_NotifyStock
{
    public function addNotifyItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setData($args['row']);
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/',
            array('id' => $product->getId(), '_secure' => true, '_nosecret' => true));
        $qty = 1 * $product->getQty();
        $description = Mage::helper('rss')->__('%s has reached a quantity of %s.', $product->getName(), $qty);
        $rssObj = $args['rssObj'];
        $storeId = Mage::getModel('core/website')->load($args['row']['website_id'])->getDefaultStore()->getId();
        $websiteName = Mage::getModel('core/website')->load($args['row']['website_id'])->getName();
        $data = array(
            'title'         => $product->getName(),
            'link'          => $url.'store/'.$storeId.'/',
            'description'   => $args['row']['low_stock_date'].': '.$description.' (website name: "'.$websiteName.'")',
        );
        $rssObj->_addEntry($data);
    }
} } 