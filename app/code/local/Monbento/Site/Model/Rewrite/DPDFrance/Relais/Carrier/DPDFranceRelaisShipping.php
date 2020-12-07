<?php

class Monbento_Site_Model_Rewrite_DPDFrance_Relais_Carrier_DPDFranceRelaisShipping extends DPDFrance_Relais_Model_Carrier_DPDFranceRelaisShipping 
{

    protected function _appendMethod($process, $row, $fees) 
    {
        $method = Mage::getModel('shipping/rate_result_method')
                ->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('title'))
                ->setMethod($row['*code'])
                // ->setMethod('dpdfrrelais')
                ->setMethodTitle($this->getConfigData('methodname') . ' ' . $this->_getMethodText($process, $row, 'label'))
                ->setMethodDescription($this->_getMethodText($process, $row, 'description')) // can be enabled if necessary
                ->setPrice($fees)
                ->setCost($fees)
        ;

        $process['result']->append($method);
    }

}

?>
