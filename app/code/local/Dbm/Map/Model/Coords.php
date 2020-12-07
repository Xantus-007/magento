<?php

class Dbm_Map_Model_Coords extends Varien_Object
{
    public function setLat($lat)
    {
        return $this->setData('lat', $lat);
    }
    
    public function getLat()
    {
        return $this->getData('lat');
    }
    
    public function setLng($lng)
    {
        return $this->setData('lng', $lng);
    }
    
    public function getLng()
    {
        return $this->getData('lng');
    }
    
    public function __toString() 
    {
        return $this->getLat().','.$this->getLng();
    }
}