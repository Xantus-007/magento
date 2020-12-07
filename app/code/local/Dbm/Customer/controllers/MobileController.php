<?php

class Dbm_Customer_MobileController extends Mage_Core_Controller_Front_Action
{
    public function switchAction()
    {
        $session = $this->_getSession();
        $session->setIsMobile(true);
        $params = $this->getRequest()->getParams();
        $trans = Mage::helper('dbm_share');
        
        //Mage::dispatchEvent(Dbm_Country_Helper_Data::EVENT_AUTO_REDIRECT);
        
        list($lang, $country) = explode('_', $locale);
        $helper = Mage::helper('dbm_country');
        $helper->setAutoRedirectCookie();
        
        if(isset($params['optim'])) {
            $session->setIsMobileOptim(true);
        }
        
        switch($params['mode'])
        {
            case 'checkout':
                $url = 'checkout/onepage';
                break;
            case 'profile':
                $url = 'club-customer/account/edit';
                break;
            case 'incompleteProfile':
                Mage::getSingleton('customer/session')->addError($trans->__('Please complete your profile'));
                $url = 'club-customer/account/edit';
                break;
            case 'register':
                $url = 'customer/account/create';
                break;
            case 'fidelity':
                $url = 'sponsorship/points/accumulated';
                break;
            case 'sponsorship':
                $url = 'sponsorship';
                break;
            case 'videos':
                $url = 'videos.html';
                break;
            case 'cgv':
                $url = 'cgv-mon-bento.html';
                break;
            case 'mentions':
                $url = 'mentions-legales-mon-bento.html';
                break;
            case 'dashboard':
                $url = 'customer/account';
                break;
            case 'cgu':
                $url = 'cgu';
                break;
            case 'lostPassword':
                $url = 'customer/account/forgotpassword';
                break;
            case 'apropos':
                $url = 'mobile-a-propos';
                break;
            case 'caracteristiques':
                $url = 'caracteristiques-produit';
                break;
            case 'aideClubento':
                $url = 'aide-clubento';
                break;
        }
        
        if(!$this->getResponse()->isRedirect())
        {
            $this->_redirect($url);
        }
    }
    
    public function unswitchAction()
    {
        $session = $this->_getSession();
        $session->setIsMobile(false);
        $session->setIsMobileOptim(false);
        
        $params = $this->getRequest()->getParams();
        switch($params['mode'])
        {
            case 'cart':
                $url = 'checkout/cart';
                break;
        }
        if(!empty($params['mode']) and !$this->getResponse()->isRedirect())
        {
            $this->_redirect($url);
        }
    }
    
    protected function _getSession()
    {
        return Mage::getModel('dbm_customer/session');
    }
}
