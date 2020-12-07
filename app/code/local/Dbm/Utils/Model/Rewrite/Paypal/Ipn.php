<?php

class Dbm_Utils_Model_Rewrite_Paypal_Ipn extends Mage_Paypal_Model_Ipn
{

    protected function _postBack(Zend_Http_Client_Adapter_Interface $httpAdapter)
    {
            $sReq = '';
            foreach ($this->_request as $k => $v) {
                $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
            }
            $sReq .= "&cmd=_notify-validate";
            $sReq = substr($sReq, 1);
            $this->_debugData['postback'] = $sReq;
            $this->_debugData['postback_to'] = $this->_config->getPaypalUrl();

            $httpAdapter->write(Zend_Http_Client::POST, $this->_config->getPaypalUrl(), '1.1', array(), $sReq);
            try {
                $response = $httpAdapter->read();
            } catch (Exception $e) {
                $this->_debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                throw $e;
            }
            $this->_debugData['postback_result'] = $response;

            /*$response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);*/
            /* Debugging PayPal IPN postback failures in Magento */
            $response = preg_split('/^\r?$/m', $response);
            $response = trim(end($response));
            if ($response != 'VERIFIED') {
                throw new Exception('PayPal IPN postback failure. See ' . self::DEFAULT_LOG_FILE . ' for details.');
            }
            unset($this->_debugData['postback'], $this->_debugData['postback_result']);
    }
}
