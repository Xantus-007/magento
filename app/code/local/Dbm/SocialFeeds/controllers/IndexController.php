<?php

class Dbm_SocialFeeds_IndexController extends Mage_Core_Controller_Front_Action
{
    public function getAllFeedsAction()
    {
        $response = array();
        
        try {
            $socialFeedsBlock = $this->getLayout()->createBlock('socialfeeds/items')
                ->setTemplate('socialfeeds/items.phtml')
                ->setData('offset', 0)
                ->setData('limit', 3)
                ->setData('size', 'small')
                ->toHtml();

            $response['block'] = $socialFeedsBlock;
            $response['status'] = 'SUCCESS';
        } catch (Exception $e) {
            $response['status'] = 'ERROR';
            Mage::logException($e);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    public function getCodeAction()
    {
        $code = $this->getRequest()->getParam('code');
        if($code)
        {
            Mage::log($code);
            $this->_generateToken($code);
        }
        exit;
    }

    protected function _generateToken($code = null)
    {
        if($code)
        {
            $instagramClientId = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_client_id');
            $instagramClientSecret = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_client_secret');
            $redirectUri = Mage::getUrl('dbm-social/index/getcode/');

            $url = 'https://api.instagram.com/oauth/access_token';
            $fields = array(
                'client_id' => $instagramClientId,
                'client_secret' => $instagramClientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code
            );

            $fields_string = '';
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result);

            Mage::log($result);

            if(isset($result->access_token)) 
            {
                Mage::getConfig()->saveConfig('dbm_feeds_config/instagram_config_general/instagram_access_token', $result->access_token, 'default', 0);
            }
        }

        return;
    }
}