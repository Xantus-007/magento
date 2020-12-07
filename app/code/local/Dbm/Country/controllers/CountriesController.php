<?php
/**
 * Description of CountriesController
 *
 * @author dlote
 */

class Dbm_Country_CountriesController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch() {
        $locale = $this->getRequest()->getParam('locale', Mage::app()->getLocale()->getLocaleCode());
        
        Mage::app()->getLocale()->setLocale($locale);
        Mage::app()->getTranslator()->init('frontend', true);
        
        parent::preDispatch();
    }
    
    public function indexAction(){
        $this->loadLayout();
        
        Mage::app()->getLocale()->setLocale('en_IE');
        Mage::app()->getTranslator()->init('frontend', true);
        $this->renderLayout();
    }
    
    public function switchAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'Dbm_Country_Block_Switch'
        );
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function getlanguagebycountryAction(){
        $result = array();
        $result['ok'] = false;
        $helper = Mage::helper('dbm_country');
        
        $countryCode = $this->getRequest()->getParam('countryCode');
        
        if($countryCode != null){
            $languageCodes = array(
                'fr' => $this->loadLayout()->__('French'), 
                'en' => $this->loadLayout()->__('English'), 
                'it' => $this->loadLayout()->__('Italian'), 
                'es' => $this->loadLayout()->__('Spanish'), 
                'de' => $this->loadLayout()->__('German')
            );
            
            foreach ($languageCodes as $key => $value) {
                $options = explode(',', Mage::getStoreConfig('dbm_country/dbm_country_countries/dbm_country_' . strtolower($key) ) );
                if(in_array($countryCode, $options)){
                    $result['language'][$key] = $value;
                    $result['ok'] = true;
                }
            }
            
            if($result['ok'] == false){
                //default
                $defaultLanguageCodes = array(
                    'fr' => $this->loadLayout()->__('French'), 
                    'en' => $this->loadLayout()->__('English'), 
                );
                
                $result['language']= $defaultLanguageCodes;
                $result['ok'] = true;
                $result['default'] = true;
            }
        }
        
        $result['urls'] = array();
        if(is_array($result['language']))
        {
            foreach($result['language'] as $langKey => $lang)
            {
                $storeView = $helper->getStoreViewByLocale($countryCode, $langKey);
                
                if($storeView && $storeView->getId())
                {
                    $url = Mage::app()->getStore($storeView->getId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'country/gateway/switch/locale/'.urlencode($langKey.'_'.strtoupper($countryCode));
                    $result['urls'][$langKey] = $url;
                }
            }
        }
        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( json_encode($result));
    }
    
    public function gettoplanguagebarAction()
    {
        $magento_block = Mage::getSingleton('core/layout');

        $result = $magento_block
                    ->createBlock('dbm_country/switch')
                    ->setTemplate('dbm/country/ajax/topbar.phtml')
                ;
        
        $messages['ok'] = true;
        $messages['html'] = $result->toHtml();

        echo Zend_Json::encode($messages);
        return true;
    }
    
    public function getpopupAction(){
        $magento_block = Mage::getSingleton('core/layout');

        $result = $magento_block
                    ->createBlock('dbm_country/switch')
                    ->setTemplate('dbm/country/ajax/popup.phtml')
                ;
        $messages['ok'] = true;
        $messages['html'] = $result->toHtml();

        echo Zend_Json::encode($messages);
        return true;
    }
    
    
}
