<?php

abstract class Dbm_Map_Controller_Abstract extends Dbm_Share_Controller_Auth
{
    protected $_bounds;
    
    protected function _construct() {
        parent::_construct();
        
        $params = $this->getRequest()->getParams();
        
        if(isset($params['SW_lat']) 
                && isset($params['SW_lng'])
                && isset($params['NE_lat'])
                && isset($params['NE_lng'])
            )
        {
            $sw = array($params['SW_lat'], $params['SW_lng']);
            $ne = array($params['NE_lat'], $params['NE_lng']);

            $this->_bounds = new Dbm_Map_Model_Bounds(array(
                Dbm_Map_Model_Bounds::SOUTH_WEST => new Dbm_Map_Model_Coords(array(
                    'lat' => floatval($sw[0]),
                    'lng' => floatval($sw[1])
                )),
                Dbm_Map_Model_Bounds::NORTH_EAST => new Dbm_Map_Model_Coords(array(
                    'lat' => floatval($ne[0]),
                    'lng' => floatval($ne[1])
                )),
            ));
        }
        
    }
}