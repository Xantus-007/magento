<?php
/**
 * Iceberg Commerce TagSearchFriendlyUrls Extension
 * 
 * Helper Class to convert tag names into url identifiers
 * and to convert back from url identifiers to tag names
 * 
 * @copyright 2009 Iceberg Commerce
 */
class IcebergCommerce_TagSearchFriendlyUrls_Helper_Tag extends Mage_Core_Helper_Abstract
{
	/**
	 * Convert from a tag name into a string we can use in a clean url
	 * @param string $tagName
	 */
    public function tagNameToUrlIdentifier($tagName)
    {
		$identifier = $tagName;
		
		// Replace all "-" in tag names to "--"
		$identifier = str_replace('-', '--', $identifier);
		
		// Replace all spaces in a tag name to "-"
		$identifier = str_replace(' ', '-', $identifier);
		
		// Replace all special characted with url encoded value 
		// (ideally there are no special characters in a tag name)
		$identifier = urldecode($identifier);
		
		return $identifier;
    }
    
    /**
     * Do the reverse of tagNameToUrlIdentifier(...)
     * @param string $identifier
     */
    public function urlIdentifierToTagName($identifier)
    {
		$tagName = $identifier;
		
		// Url decode first
		$tagName = urldecode($tagName);
		
		// Temporarily replace all "--" with a dummy value
		$tagName = str_replace('--', 'XXDUMMYSTRINGXX', $tagName);
		
		// Replace all "-" to spaces
		$tagName = str_replace('-', ' ', $tagName);
		
		// Replace dummy value with a "-"
		$tagName = str_replace('XXDUMMYSTRINGXX', '-', $tagName);

		return $tagName;
    }
}
