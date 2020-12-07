<?php

class Wizkunde_ConfigurableBundle_Model_Resource_Image_Image extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('configurablebundle/image_image', 'id');
    }
}