<?php

class Dbm_Seo_Adminhtml_IndexController extends Mage_Core_Controller_Front_Action {

    public function redirectionindexAction()
    {
        $status = $this->_getHttpStatus(Mage::getBaseUrl().'index.php');

        if($status == 301) {
            $check = "La redirection est en place";
        } else {
            $check = "La redirection n'est pas en place";
        }

        $this->getResponse()->setBody($check);
        return;
    }

    public function w3cAction() 
    {
        $home = Mage::getBaseUrl();
        $html = file_get_contents($home);

        $response = $this->_postDataReturnPage('https://validator.w3.org/nu/', 'content='.urlencode($html));
        $this->getResponse()->setBody($response);
        return;
    }
    
    protected function _getHttpStatus($url) 
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, false);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($handle, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($handle);
        $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            
        curl_close($handle);

        return $httpCode;
    }

    protected function _postDataReturnPage($url, $postData)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POST, $postData);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($handle, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($handle);
        $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            
        curl_close($handle);

        return $response;
    }
}