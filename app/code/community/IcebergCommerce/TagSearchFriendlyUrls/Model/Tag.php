<?php
/**
 * Iceberg Commerce TagSearchFriendlyUrls Extension
 * 
 * Rewrite the Mage_Tag_Model_Tag Class so that we can redefine the getTaggedProductsUrl() method.
 * The getTaggedProductsUrl() method returns the link for a product tag.
 * 
 * We are returning a link that looks like:
 * 		http://store.com/tag/tag-name 
 * instead of
 * 		http://store.com/tag/product/list/tagId/X
 * 
 * @copyright 2009 Iceberg Commerce
 */
class IcebergCommerce_TagSearchFriendlyUrls_Model_Tag extends Mage_Tag_Model_Tag
{
    public function getTaggedProductsUrl()
    {
    	$identifier = Mage::helper('icebergcommerce_tagsearchfriendlyurls/tag')->tagNameToUrlIdentifier($this->getName());
        
    	$storeUrl = Mage::getUrl('tag');
    	
    	$url = $storeUrl . $identifier;
    	
        return $url;
    }
}