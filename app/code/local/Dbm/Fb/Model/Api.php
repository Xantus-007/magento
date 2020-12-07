<?php

class Dbm_Fb_Model_Api extends Mage_Core_Model_Abstract
{
    protected $_fb;
    protected $_appId;
    protected $_appSecret;

    protected $_linkTableName;

    public function _construct() {
        parent::_construct();

        require_once Mage::getModuleDir('', 'Dbm_Fb').DS.'lib'.DS.'base_facebook.php';
        require_once Mage::getModuleDir('', 'Dbm_Fb').DS.'lib'.DS.'facebook.php';

        $this->_linkTableName = Mage::getSingleton('core/resource')->getTableName('belvg_facebook_customer');
    }

    protected function _initApi($autoConnect = true)
    {
        $trans = Mage::helper('dbm_share');
        
        if(!$this->_fb)
        {
            $this->_appId = Mage::getStoreConfig('facebookfree/settings/appid');
            $this->_appSecret = Mage::getStoreConfig('facebookfree/settings/secret');

            $this->_fb = new Facebook(array(
                'appId' => $this->_appId,
                'secret' => $this->_appSecret
            ));
	    
            if($autoConnect)
            {
                $user = $this->_fb->getUser();

                if(!$user)
                {
                    Mage::throwException($trans->__('Cannot connect to Facebook'));
                }
            }
        }
    }

    public function getFriendsIds()
    {
        $this->_initApi(false);

        $result = array();

        $user = $this->_fb->getUser();
        if($user > 0) {
            $friends = $this->_fb->api('/me/friends');

            foreach($friends['data'] as $friend)
            {
                $result[] = $friend['id'];
            }
        }

        return $result;
    }

    public function findMyFriends()
    {
        $friendsIds = $this->getFriendsIds();

        $collection = $this->findFBFriendsFromArray($friendsIds);

        return $collection;
    }

    public function findFBFriendsFromArray($friendsIds)
    {
        $result = null;

        if(is_array($friendsIds) && count($friendsIds))
        {

            $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
            $select = $collection->getSelect();

            $filtererd = array();

            foreach($friendsIds as $fId)
            {
                $filtererd[] = intval($fId);
            }

            $select->join(array('fb_link' => $this->_linkTableName),
                'e.entity_id = fb_link.customer_id',
                'fb_link.fb_id as fb_id'
            )
                ->where('fb_id IN ('.implode(', ', $filtererd).')')
            ;

            $result = $collection;
        }

        return $result;

    }
    
    public function customerLogin($fbId, $accessToken)
    {
        $this->_initApi(false);
        $me = json_decode($this->_getFbData('https://graph.facebook.com/me?access_token=' . $accessToken));
//Mage::log('ME : '.print_r($me, true), null, 'api.xml');        
$me = (array)$me;
        $trans = Mage::helper('dbm_share');
Mage::log('FB LOGIN', null, 'api.xml');
        
        if(is_null($me) || !isset($me['id']))
        {
            Mage::throwException($trans->__('Error while connecting'));
        }
        
        $session = Mage::getSingleton('customer/session');
        
        $db_read = Mage::getSingleton('core/resource')->getConnection('facebookfree_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $sql = 'SELECT `customer_id`
            FROM `' . $tablePrefix . 'belvg_facebook_customer`
            WHERE `fb_id` = ' . $me['id'] . '
            LIMIT 1';
        $data = $db_read->fetchRow($sql);

        if ($data)
        {
            $session->loginById($data['customer_id']);
        } 
        else 
        {
            $sql = 'SELECT `entity_id`
                FROM `' . $tablePrefix . 'customer_entity`
                WHERE email = "' . $me['email'] . '"
                -- AND store_id = "'.Mage::app()->getStore()->getStoreId().'"
                -- AND website_id = "'.Mage::getModel('core/store')->load(Mage::app()->getStore()->getStoreId())->getWebsiteId().'"
                LIMIT 1';
            $r = $db_read->fetchRow($sql);

            if ($r) 
            {
                $db_write = Mage::getSingleton('core/resource')->getConnection('facebookfree_write');
                $sql = 'INSERT INTO `' . $tablePrefix . 'belvg_facebook_customer`
                                                VALUES (' . $r['entity_id'] . ', ' . $me['id'] . ')';
                $db_write->query($sql);
                $session->loginById($r['entity_id']);
            }
            else 
            {
                $this->_registerCustomer($me, $session);
            }
        }
        
        return  Mage::helper('customer')->isLoggedIn();
    }
    
    private function _registerCustomer($data, &$session)
    {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer->setData('firstname', $data['first_name']);
        $customer->setData('lastname', $data['last_name']);
        $customer->setData('email', $data['email']);
        $customer->setData('password', md5(time() . $data['id'] . $data['locale']));
        $customer->setData('is_active', 1);
        $customer->setData('confirmation', null);
        $customer->setConfirmation(null);
        $customer->getGroupId();
        $customer->save();

        Mage::getModel('customer/customer')->load($customer->getId())->setConfirmation(null)->save();
        $customer->setConfirmation(null);
        $session->setCustomerAsLoggedIn($customer);
        $customer_id = $session->getCustomerId();
        $db_write = Mage::getSingleton('core/resource')->getConnection('facebookfree_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $sql = 'INSERT INTO `' . $tablePrefix . 'belvg_facebook_customer`
				VALUES (' . $customer_id . ', ' . $data['id'] . ')';
        $db_write->query($sql);
    }

    private function _getFbData($url)
	{
		$data = null;

		if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
			$data = file_get_contents($url);
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
		}
		return $data;
	}

}
