<?php
/**
 * Description of Locale
 *
 * @author dlote
 */
class Dbm_Country_Model_Core_Locale extends Mage_Core_Model_Locale{
    
    /**
     * Set locale
     *
     * @param   string $locale
     * @return  Mage_Core_Model_Locale
     */
//    public function setLocale($locale = null)
//    {
//        Mage::log('dbm SetLocale');
//        $cookieLocale = Mage::getModel('core/cookie')->get('dbm_country_switch_language');
//        
//        if (($locale !== null) && is_string($locale)) {
//            if($cookieLocale == null || $cookieLocale == ''){
//                $this->_localeCode = $locale;
//            }else{
//                $this->_localeCode = $cookieLocale;
//            }
//        } else {
//            if($cookieLocale == null || $cookieLocale == ''){
//                $this->_localeCode = $this->getDefaultLocale();
//            }else{
//                $this->_localeCode = $cookieLocale;
//            }
//        }
//        Mage::dispatchEvent('core_locale_set_locale', array('locale'=>$this));
//        return $this;
//    }
    
    
    /**
    * Retrieve default locale code
    *
    * @return string
    */
//  public function getDefaultLocale()
//   {
//       if (!$this->_defaultLocale) {
//           
//            $cookieLocale = Mage::getModel('core/cookie')->get('dbm_country_switch_language');
//            if($cookieLocale == null || $cookieLocale == ''){
//                $locale = Mage::getStoreConfig(self::XML_PATH_DEFAULT_LOCALE);
//                if (!$locale) {
//                    $locale = self::DEFAULT_LOCALE;
//                }
//                $this->_defaultLocale = $locale;
//            }else{
//                
//                $locale = $cookieLocale;
//                $this->_defaultLocale = $locale;
//            }
//       }
//       return $this->_defaultLocale;
//   }
}


