<?php 

class Dbm_Share_Model_Localized_Abstract extends Mage_Core_Model_Abstract
{
    public function __call($name, $args)
    {
        $localizedFields = Mage::helper('dbm_share')->getLocalizedFields();
        $filter = new Zend_Filter_Word_CamelCaseToUnderscore();
        $testName = strtolower($filter->filter($name));

        foreach($localizedFields as &$field)
        {
            $field = 'get_'.$field;
        }

        if(in_array($testName, $localizedFields))
        {
            $testName = str_replace('get_', '', $testName);
            return $this->_getLocalizedData($testName);
        }
        else return parent::__call($name, $args);
    }

    protected function _getLocalizedData($field)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        
        $testLocales = Mage::helper('dbm_share')->getDefaultLocaleForStoreId($storeId);
        $testLocales[] = 'en_gb';
        
        $localeBrowser = new Zend_Locale();
        if($localeBrowser->getLanguage() == "pt" or $localeBrowser->getLanguage() == "ja") {
            if($localeBrowser->getLanguage() == "pt") array_unshift($testLocales, "pt_pt");
            if($localeBrowser->getLanguage() == "ja") array_unshift($testLocales, "ja_jp");
        }
        
        $lastFound = null;

        foreach($testLocales as $locale)
        {
            $data = $this->getData($field.'_'.$locale);

            if(strlen($data))
            {
                $finalValue = $data;
                break;
            }
        }

        if(!strlen($finalValue))
        {
            $locales = Mage::helper('dbm_share')->getAllowedLocalesWithoutExcludeLocales();

            foreach($locales as $locale)
            {
                $data = $this->getData($field.'_'.$locale);

                if(strlen($data))
                {
                    $finalValue = $data;
                    break;
                }
            }
        }
        return $finalValue;
    }
}