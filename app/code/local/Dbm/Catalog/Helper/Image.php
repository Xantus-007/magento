<?php

class Dbm_Catalog_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getSizes()
    {
        return array(
            'bundle_thumbnail' => array(512, 512)
        );
    }
}