<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogProductHelperFormGallery.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ qorySkjDCDqhwwah('40c7f1e5f095b6c0841257b852bf3764'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductHelperFormGallery
    extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery
{
	public function getElementHtml()
	{
		$html = parent::getElementHtml();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
		{
            $html = preg_replace(
                '@cell-remove a-center last"><input([ ]+)type="checkbox"@',
                'cell-remove a-center last"><input disabled="disabled" type="checkbox"',
                $html
            );
		}

        return $html;
	}
} } 