<?php

class Monbento_Site_Model_Rewrite_DPDFrance_Predict_Carrier_DPDFrancePredictShipping extends DPDFrance_Predict_Model_Carrier_DPDFrancePredictShipping 
{

    protected function _appendMethod($process, $row, $fees) 
    {
        $method = Mage::getModel('shipping/rate_result_method')
                ->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('title'))
                ->setMethod($row['*code'])
                // ->setMethod('dpdfrpredict')
                ->setMethodTitle($this->getConfigData('methodname') . ' ' . $this->_getMethodText($process, $row, 'label'))
                ->setMethodDescription($this->_getMethodText($process, $row, 'description')) // can be enabled if necessary
                ->setPrice($fees)
                ->setCost($fees)
        ;

        $process['result']->append($method);
    }

}

?>
