<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ mfrfthfEqrhBkfDm('ffc61c7c1bb96871dc26cb8e17ebfcdc');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Mysql4_Core_Website extends Mage_Core_Model_Mysql4_Website
{
// start aitoc code    
    public function getIdByCode($sCode)
    {
        if (!$sCode) return false;
        
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('website_id'))
            ->where('code=?', $sCode);

        return $this->_getReadAdapter()->fetchOne($select);
    }
// finish aitoc code    
    
} } 