<?php
/**
 * Iceberg Commerce TagSearchFriendlyUrls Extension
 * 
 * This Router class handles requests for the new clean urls for tags
 * in the form of http://store.com/tag/tag-name
 * 
 * Requests that match this route are re-routed to /tag/product/list/tagId/X
 * 
 * @copyright 2009 Iceberg Commerce
 */

/**
 * Router for SEF Tag Urls
 */
class IcebergCommerce_TagSearchFriendlyUrls_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $tagSearchFriendlyUrls = new IcebergCommerce_TagSearchFriendlyUrls_Controller_Router();
        $front->addRouter('icebergcommerce_tagsearchfriendlyurls', $tagSearchFriendlyUrls);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        // Extract the tag name from the url
        $identifier = trim($request->getPathInfo(), '/');

        $parts = explode('/', $identifier);
        
        if (count($parts)!=2)
        {
        	return false;
        }
        
        if (trim(strtolower($parts[0]))!='tag')
        {
        	return false;
        }
        
    	$tagName = Mage::helper('icebergcommerce_tagsearchfriendlyurls/tag')->urlIdentifierToTagName($parts[1]);

    	// Load the tag by name
		$tag = Mage::getModel('tag/tag')->loadByName($tagName);

		if(!$tag->getId())
		{
			return false;
		}
		
		// The loadbyname method does not populate store visibility, so load again using the load method
		$tag = Mage::getModel('tag/tag')->load($tag->getId());
		
		// isAvailableInStore is not available in Mage 1.3.*
		if(!$tag->getId() /*|| !$tag->isAvailableInStore()*/)
		{
			return false;
		}
		
		// Now do the routing through the regular tag router using the clean alias
        $request->setModuleName(isset($d[0]) ? $d[0] : 'tag')
            ->setControllerName(isset($d[1]) ? $d[1] : 'product')
            ->setActionName(isset($d[2]) ? $d[2] : 'list')
            ->setParam('tagId', $tag->getId());
            
		$request->setAlias(
			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
			$identifier
		);
		
        return true;
    }
}