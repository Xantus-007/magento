<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */

/**
 * Used in creating options for Socolissimo Contract selection
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Adminhtml_System_Config_Source_Contratsocolissimo
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $observer = Mage::getSingleton('socolissimo/observer');
        
        $cfgData = Mage::getSingleton('adminhtml/config_data');
        $storeId = $cfgData->getScopeId();
        
        if ($observer->_9cd4777ae76310fd6977a5c559c51820($storeId, Addonline_SoColissimo_Model_Observer::CONTRAT_FLEXIBILITE)) {
        }
        $options[] = array(
            'value' => 'flexibilite',
            'label' => Mage::helper('socolissimo')->__('Flexibilité')
        );
        if ($observer->_9cd4777ae76310fd6977a5c559c51820($storeId, Addonline_SoColissimo_Model_Observer::CONTRAT_LIBERTE)) {
        }
        $options[] = array(
            'value' => 'liberte',
            'label' => Mage::helper('socolissimo')->__('Liberté')
        );
        return $options;
    }
}
