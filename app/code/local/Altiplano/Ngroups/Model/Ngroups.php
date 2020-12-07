<?php

class Altiplano_Ngroups_Model_Ngroups extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ngroups/ngroups');
    }
   
}