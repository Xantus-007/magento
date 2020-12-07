<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ ZUeUPwUDhewkjUMZ('84c4c66e5d30c8e2f70856f1da967871');
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

class Aitoc_Aitquantitymanager_Model_Mysql4_Core_Website_Collection extends Mage_Core_Model_Mysql4_Website_Collection
{
    public function load($printQuery = false, $logQuery = false)
    {
// start aitoc code 
       
        $this->getSelect()->where('main_table.code != "aitoccode" ');

// finish aitoc code

        parent::load($printQuery, $logQuery);
        return $this;
    }

} } 