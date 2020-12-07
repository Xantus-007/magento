<?php

class Dbm_Share_Model_Order_Attribute_Origin
{
    const ORIGIN_DESKTOP = 0;
    const ORIGIN_IOS = 1;
    const ORIGIN_IOS_APP = 10;
    const ORIGIN_ANDROID = 2;
    const ORIGIN_ANDROID_APP = 20;
    const ORIGIN_MOBILE = 3;
    
    public function getOriginLabel($id)
    {
        $labels = $this->_getLabels();
        return $labels[$id];
    }
    
    public function getLabels()
    {
        return $this->_getLabels();
    }
    
    protected function _getLabels()
    {
        return array(
            self::ORIGIN_DESKTOP => 'Desktop',
            self::ORIGIN_IOS => 'Site iOS',
            self::ORIGIN_IOS_APP => 'Application iOS',
            self::ORIGIN_ANDROID => 'Site Android',
            self::ORIGIN_ANDROID_APP => 'Application Android'
        );
    }
}