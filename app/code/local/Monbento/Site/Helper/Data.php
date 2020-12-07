<?php

class Monbento_Site_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $CMS_PAGE_FOR_HN_LOGO = array('home-page-v2', 'shop');
    
    public function isHomePage()
    {
        return $this->_checkTypeOfPage('home');
    }
    
    public function isCartPage()
    {
        return $this->_checkTypeOfPage('cart');
    }
    
    public function isCheckout()
    {
        return $this->_checkTypeOfPage('checkout');
    }
    
    public function isInAccount()
    {
        return $this->_checkTypeOfPage('account');
    }
    
    public function isInClub()
    {
        return $this->_checkTypeOfPage('club');
    }
    
    public function isInCms()
    {
        return $this->_checkTypeOfPage('cms');
    }
    
    public function isInRecrutement()
    {
        return (Mage::getSingleton('cms/page')->getIdentifier() == 'candidature-spontanee' || in_array(Mage::getSingleton('cms/page')->getRootTemplate(), array('listing-recrutement', 'recrutement')));
    }
    
    public function getAproposPage()
    {
        return $this->_getCmsPageByTemplate('a-propos');
    }
    
    public function getEquipePage()
    {
        return $this->_getCmsPageByTemplate('equipe');
    }
    
    public function getRecrutementPage()
    {
        return $this->_getCmsPageByTemplate('listing-recrutement');
    }
    
    public function getRetailersUrl()
    {
        $storeId = Mage::app()->getStore()->getId();
        
        switch($storeId)
        {
            case 7:
            case 1:
                $url = 'espace-pro-mon-bento';
                break;
            case 4:
            case 8:
                $url = 'profesionales';
                break;
            case 3:
                $url = 'professionisti';
                break;
            case 5:
                $url = 'geschaeftskundenbereich';
                break;
            default:
                $url = 'retailers';
                break;
        }
        
        return Mage::getUrl($url);
    }
    
    public function getRetailShopsUrl()
    {
        $storeId = Mage::app()->getStore()->getId();
        
        switch($storeId)
        {
            case 7:
            case 1:
                $url = 'a-propos/ou-nous-trouver';
                break;
            case 4:
            case 8:
                $url = 'donde-encontranos';
                break;
            case 3:
                $url = 'dove-siamo';
                break;
            case 5:
                $url = 'wo-finden-sie-uns';
                break;
            default:
                $url = 'retail-shop.html';
                break;
        }
        
        return Mage::getUrl($url);
    }
    
    public function menuShopIsNotOpen()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if($this->isInAccount() || $this->isCartPage() || $this->isInClub() || $this->isInCms() || $this->isCheckout()) return true;
        
        return false;
    }
    
    public function getProductAttributeText($_product, $attribute)
    {
        return $_product->getResource()->getAttribute($attribute)->getSource()->getOptionText($_product->getData($attribute));
    }
    
    public function getOptionTextByOptionId($attribute, $optionId)
    {
        return Mage::getModel('catalog/product')->getResource()->getAttribute($attribute)->getSource()->getOptionText($optionId);
    }
    
    public function getChildImage($_product, $optionId)
    {
        $declinaisons = Mage::helper('dbm_utils/product')->getDeclinaisons($_product);
        foreach($declinaisons as $decli)
        {
            if($decli->getVarianteCouleur() == $optionId)
            {
                if($decli->getImage() != 'no_selection') return array('large' => Mage::helper('catalog/image')->init($decli, 'image'), 'medium' => Mage::helper('catalog/image')->init($decli, 'image')->resize(620, 540));
            }
        }
        
        return null;
    }
    
    public function getProductVisuelPromo($_product)
    {
        $attributeText = $_product->getResource()->getAttribute('visuel_promotionnel')->setStoreId(0)->getSource()->getOptionText($_product->getData('visuel_promotionnel'));
        
        $picto = '';
        
        $visuelPromo = array(
            'Remise' => 'alt--discount',
            'Soldes' => 'alt--sales',
            'Nouveau' => 'main',
            'Precommande' => 'alt--preorder',
        );
        
        if(isset($visuelPromo[$attributeText])) $picto = $visuelPromo[$attributeText];

        return $picto;
    }
    
    public function getFilterColorByAttributeOption($value)
    {
        $colors = array(
            'blanc' => 'ffffff',
            'bleu' => '345e7c',
            'bleu-ciel' => '65c4df',
            'bleu-fonce' => '405e7a',
            'corail' => 'db5f6c',
            'fushia' => 'a9398f',
            'gris' => 'a69d94',
            'gris-fonce' => '636567',
            'ice' => 'bfc9d5',
            'iceberg' => 'b5d4dd',
            'jaune' => 'f4d717',
            'lilas' => 'cab8cb',
            'litchi' => 'e2bfc5',
            'marron' => '826d5e',
            'matcha' => 'b5d5bd',
            'noir' => '292929',
            'orange' => 'e97e1d',
            'peach' => 'dbb6aa',
            'rose' => 'dd7eb1',
            'rouge' => 'd3424b',
            'vanilla' => 'd8cfc8',
            'vert' => '8abf3a',
            'moutarde' => 'e6ab55',
            'bleu-roi' => '003892',
            'denim' => '597d90',
            'vert-sapin' => '3f5b58',
            'coton' => 'c8c8c8',
            'english-garden' => 'afb6a0',
            'brique' => 'c36c54',
            'blush' => 'a46a7c',
            'navy' => '33436a',
            'apple' => 'c8d998'
        );
        
        return (isset($colors[$value])) ? $colors[$value] : 'ffffff';
    }
    
    public function showHnForLogo()
    {
        $result = false;
        if($currentIdent = Mage::getSingleton('cms/page')->getIdentifier())
        {
            $pageId = Mage::getSingleton('cms/page')->getIdentifier();
            if(in_array($pageId, $this->CMS_PAGE_FOR_HN_LOGO)) return true;
        }
    }
    
    public function getFrancoMsgForMonbento()
    {
        $cHelper = Mage::helper('checkout/cart');
        $cart = $cHelper->getQuote()->getData();
        
        if(!isset($cart['grand_total']))
        {
            $amount = '';
        }
        else
        {
            $amount = $cart['grand_total'];
        }
            
        $franco = Mage::getStoreConfig('monbento_config/monbento_config_general/monbento_franco_amount');
        $result = $franco - $amount;
        
        return ($result <= 0) ? false : $result;
    }
    
    protected function _getCmsPageByTemplate($template = 'a-propos')
    {
        $page = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('root_template', array('eq' => $template))
                ->getFirstItem();
        
        if(count($page) > 0)
        {
            return $page;
        }
        else
        {
            return false;
        }
    }
    
    protected function _checkTypeOfPage($pageType = '')
    {
        $request = Mage::app()->getFrontController()->getRequest();

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        switch($pageType)
        {
            case 'account':
                $modulesForTest = array('customer', 'dbm-customer', 'newsletter', 'sales', 'sponsorship');
                break;
            case 'cart':
                $modulesForTest = array('dbmAjaxAddToCart', 'monbento-site');
                break;
            case 'checkout':
                $modulesForTest = array('checkout', 'oyecheckout', 'paypal');
                break;
            case 'club':
                $modulesForTest = array('blog', 'club', 'club-customer');
                break;
            case 'home':
            case 'cms':
                $modulesForTest = array('cms');
                break;
            default:
                $modulesForTest = array();
                break;
        }
        
        if(in_array($module, $modulesForTest))
        {
            if($pageType == 'cart' && ($controller != 'cart' || $action != 'index')) return false;
            if($pageType == 'cms' && ($request->getOriginalPathInfo() == '/shop/' || $request->getOriginalPathInfo() == '/')) return false;
            if($pageType == 'home' && Mage::getSingleton('cms/page')->getIdentifier() != 'home-page-v2') return false;
            
            return true;
        }
        
        return false;
    }

    public function quoteWithFreeShipping($quote)
    {
        $rules = $quote->getAppliedRuleIds();
        $rules = explode(',', $rules);

        $freeShipping = false;
        foreach ($rules as $_ruleId) 
        {
            $rule = Mage::getModel('salesrule/rule')->load($_ruleId);
            if($rule->getSimpleFreeShipping() == 2) $freeShipping = true;
        }

        return $freeShipping;
    }

    public function getSavType()
    {
        return array(
            '' => '',
            'type1' => 'Mauvais article envoyé',
            'type2' => 'Article manquant',
            'type3' => 'Article manquant en production',
            'type4' => 'Colis égaré/non livré',
            'type5' => 'Défaut fabrication à ouverture(1mois)',
            'type6' => 'Article défectueux(1an)',
            'type8' => 'Vol en magasin',
            'type9' => 'SAV client final',
            'type10' => 'Geste commercial',
            'type11' => 'Article cassé lors du transport',
            'type12' => 'Produit démonstration magasin',
            'type13' => 'Geste commercial',
            'type14' => 'Packaging abimé',
            'type15' => 'Echantillons commercial',
            'type16' => 'Echantillons influenceur',
            'type17' => 'Echantillons partenariat',
            'type18' => 'Echantillons shooting photo',
            'type19' => 'Cadeau',
            'type20' => 'Autres'
        );
    }

public function getKeyUrlPageCms($storeCode)
{

switch ($storeCode) {
    case 'fr':
        return '/charte-protection-donnees-personnelles-gestion-cookies';
        break;
    case 'it':
        return '/informativa-protezione-dati-personali-gestione-cookies';
        break;
    case 'de':
        return '/personliche-datenschutz-cookie-verwaltung-charta';
        break;
    case 'es':
        return '/carta-proteccion-datos-personales-gestion-cookies';
        break;
    default:
        return '/personal-data-protection-cookie-management-charter';
}
}
}
