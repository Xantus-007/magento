<?php

class Dbm_Map_Model_Bounds extends Varien_Object
{
    const SOUTH_WEST = 'south_west';
    const NORTH_EAST = 'north_east';
    
    public function setSouthWest(Dbm_Map_Model_Coords $coords)
    {
        $this->setData(self::SOUTH_WEST, $data);
    }
    
    public function getSouthWest()
    {
        return $this->getData(self::SOUTH_WEST);
    }
    
    public function setNorthEast(Dbm_Map_Model_Coords $coords)
    {
        return $this->setData(self::NORTH_EAST, $coords);
    }
    
    public function getNorthEast()
    {
        return $this->getData(self::NORTH_EAST);
    }
}