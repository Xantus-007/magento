<?php

class Dbm_Share_Model_Api_V2 extends Dbm_Customer_Model_Api_Auth_Abstract
{
    public function getConfig($locale = 'fr_FR')
    {
        $localeParts = explode('-', $locale);
        if(count($localeParts) > 1)
        {
            $locale = $localeParts[1];
        }

        if($locale == 'gb_gb') $locale = 'en_GB';
        if($locale == 'us_us') $locale = 'en_US';
        if($locale == 'us_gb') $locale = 'en_US';
        if($locale == 'hk_gb') $locale = 'en_HK';
        
        $helper = Mage::helper('dbm_share/api');
	    Mage::log('GETTING LOCALE : '.$locale, null, 'debug.log');

        $locale = strtolower($locale);
        $locale = $helper->testLocale($locale);
        list($lang, $country) = explode('_', $locale);

        Mage::log('TEST : '.$lang.' '.$country, null, 'debug.log');
        
        if(strlen($lang) > 2 or strlen($country) > 2) {
            $lang = 'en';
            $country = 'ie';
            $locale = 'en_ie';
        }
        
        $testLocale = Mage::helper('dbm_country')->getStoreViewByLocale($country, $lang);
        
        Mage::log('TEST LOCALE : '.print_r($testLocale->getId(), true), null, 'debug.log');
 
        $storeView = $helper->getDefaultStoreView($locale);
        $storeView = $testLocale->getId();
        $this->_setStoreId($storeView);

	Mage::log('SETTING STORE VIEW : '.$this->_setStoreId($storeView), null, 'debug.log');
        
        return Mage::helper('dbm_share/api')->getFullConfig($locale);
    }
    
    public function postPhoto($type, $data)
    {
        return $this->_post($type, $data);
    }
    
    public function postReceipe($type, $data)
    {
        return $this->_post($type, $data);
    }
    
    public function elementDelete($idElement)
    {
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();
        $element = Mage::getModel('dbm_share/element');
        $element->load($idElement);
        
        if($element->getId() > 0 && $customer->getId() > 0)
        {
            $element->apiDelete($customer);
        }
        
        return true;
    }
    
    protected function _post($type, $data)
    {
        $this->_checkCustomerAuthentication();
        $element = Mage::getModel('dbm_share/element');
        $customer = $this->_getAuthenticatedCustomer();
        
        return $element->saveElementFromApi($type, $customer, $data);
    }
    
    public function getLikedElements($type, $page = 0)
    {
        $this->_checkCustomerAuthentication();
        $helper = Mage::helper('dbm_share');
        $customer = $this->_getAuthenticatedCustomer();
        
        if($helper->isTypeAllowed($type))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->addLikedByFilter($customer)
                ->setCurPage($page)
                ->orderByDate()
            ; 
            
            $result = $collection->toApiArray();
        }
        
        return $result;
    }
    
    public function getElementById($idElement)
    {
        $collection =  Mage::getModel('dbm_share/element')->getCollection()
            ->addAll()
            ->setApiDefaults()
            ->orderByLikes()
            ->addFieldToFilter('id', $idElement)
        ;
        
        return $collection->getFirstItem()->toApiArray();
    }
    
    public function getPopularElements($type, $page = 0)
    {
        $helper = Mage::helper('dbm_share');
        $result = array();

        //$this->_checkCustomerAuthentication();
        
        if($helper->isTypeAllowed($type))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->setCurPage($page)
                ->orderByLikes()
            ;

            $result = $collection->toApiArray();
        }

        return $result;
    }

    public function getLatestElements($type, $page = 0)
    {
        $helper = Mage::helper('dbm_share');
        $result = array();

        if($helper->isTypeAllowed($type))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->setCurPage($page)
                ->orderByDate();

            $result = $collection->toApiArray();
        }

        return $result;
    }
    
    public function getFollowedElements($type, $page = 0)
    {
        $helper = Mage::helper('dbm_share');
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();
        
        if($helper->isTypeAllowed($type))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->setCurPage($page)
                ->addFollowedByFilter($customer)
                ->orderByDate();
            
            $result = $collection->toApiArray();
        }
        
        Mage::getModel('dbm_customer/customer')->updateNotifications($customer);
        
        return $result;
    }
    
    public function getElementsForCustomer($idCustomer, $type, $page = 0)
    {

        Mage::log('GET ELEMENTS FOR CUSTOMER');

        $result = array();
        $helper = Mage::helper('dbm_share');
        $customer = Mage::getModel('customer/customer');

        if($helper->isTypeAllowed($type) && $customer->load($idCustomer))
        {
            $collection =  Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->addCustomerFilter($customer)
                ->setCurPage($page)
                ->orderByDate()
            ;

            $result = $collection->toApiArray();
        }

        if(is_array($result)):
        foreach($result as $resultItem)
        {
            Mage::log('TEST : '.$resultItem['id'] . ' - '.$resultItem['title_fr_fr']);
        }
        endif;

        return $result;
    }

    public function getElementsForCategory($idCategory, $type, $locales = array(), $page = 0)
    {
        $result = array();
        $category = Mage::getModel('dbm_share/category');
        $category->load($idCategory);
        
        Mage::log('LOCALES RECETTES : '.implode('-', $locales), null, 'debug.log');
        
        $formattedLocales = $this->_formatLocales($locales);
        $helper = Mage::helper('dbm_share');
        if($helper->isTypeAllowed($type))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->setCurPage($page)
            ;
            
            //Ajouter popular ID
            $collection->addLocaleFilter($formattedLocales);
            
            if($idCategory == Dbm_Share_Model_Category::POPULAR_ID)
            {
                $collection->orderByLikes();
                $collection->getSelect()->order('created_at DESC');
            }
            else
            {
                if($category->getId())
                {
                    $collection->addCategoryFilter($category);
                }
                
                $collection->orderByDate();
            }
            
            $result = $collection->toApiArray();
        }

        return $result;
    }

    public function like($idElement){
        $result = true;
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();
        $element = Mage::getModel('dbm_share/element')->load($idElement);
        if($element->getId())
        {
            $element->like($customer);
        }

        return $result;
    }

    public function unlike($idElement)
    {
        $result = true;
        $this->_checkCustomerAuthentication();

        $customer = $this->_getAuthenticatedCustomer();
        $element = Mage::getModel('dbm_share/element');
        if($element->load($idElement))
        {
            $element->unlike($customer);
        }

        return $result;
    }

    public function getCategoryList($type)
    {
        $result = array();
        $result = Mage::getModel('dbm_share/category')->getListForType($type)->sortByPosition()->toApiArray();

        return $result;
    }

    public function comment($idElement, $message)
    {
        $this->_checkCustomerAuthentication();
        $result = Mage::getModel('dbm_share/comment')->saveCommentForCurrentCustomer($idElement, $message);
        return $result ? true : false;
    }
    
    public function getComments($idElement, $page = 0)
    {
        $element = Mage::getModel('dbm_share/element')->load($idElement);
        if($element->getId() > 0)
        {
            $result = $element->getComments()
                ->setApiDefaults()
                ->setCurPage($page)
                ->toApiArray()
            ;
        }
        
        return $result;
    }
    
    public function abuseElement($idElement)
    {
        return true;
    }

    public function abuseComment($idComment)
    {
        return true;
    }

    public function search($searchString, $type = 'all', $locales = array(), $page = 0)
    {
        $formattedLocales = $this->_formatLocales($locales);
        
        $collection = Mage::getModel('dbm_share/element')->getCollection()->addAll()
            ->setApiDefaults()
            ->addLocaleFilter($formattedLocales)
            ->setCurPage($page)
            ->orderByLikes()
        ;
        
        $result = $collection->search($searchString, $type);
        
        return $result->toApiArray();
    }
    
    public function searchFromBounds($bounds, $type)
    {
        $SW = new Dbm_Map_Model_Coords(array(
            'lat' => $bounds->south_west->lat, 
            'lng' => $bounds->south_west->lng
        ));
        $NE = new Dbm_Map_Model_Coords(array(
            'lat' => $bounds->north_east->lat, 
            'lng' => $bounds->north_east->lng
        ));
        
        $bounds = new Dbm_Map_Model_Bounds(array(
            'south_west' => $SW, 
            'north_east' => $NE
        ));
        
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addLikes()
            ->addTypeFilter($type)
            ->addBoundsFilter($bounds)
        ;
        
        return $collection->toApiArray();
    }
    
    protected function _formatLocales($locales)
    {
        $formattedLocales = array();
        if(is_array($locales))
        {
            foreach($locales as $locale)
            {
                $formattedLocales[$locale] = 1;
            }
        }
        
        return $formattedLocales;
    }
}
