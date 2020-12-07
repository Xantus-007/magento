<?php

class Dbm_Share_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MAIN_MEDIA_FOLDER = 'share';
    const GMAPS_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/json';
    const GMAPS_GEOCODE_REVERSE_URL  = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&sensor=false';
    const API_LIST_PAGE_SIZE = 10;
    const DURATION_MINUTES = 1;
    const DURATION_HOURS = 2;
    const CONFIGURABLE_PRODUCT = 3380;

    const COMMENT_STATUS_BANNED = -1;
    const COMMENT_STATUS_INACTIVE = 0;
    const COMMENT_STATUS_ACTIVE = 1;

    const EVENT_ADD = 'dbm_element_add';
    const EVENT_REMOVE = 'dbm_element_remove';

    const REG_MENU_TOP = 'top_alias';
    const REG_MENU_MAIN = 'main_alias';
    
    const LOGGED_IN_CLASS = 'loggedIn';
    const NOT_LOGGED_IN_CLASS = 'notLoggedIn';
    
    public function getAllowedLocales()
    {
        return array(
            'fr_fr',
            'en_gb',
            'en_ie',
            'ja_jp',
            'es_es',
            'pt_pt'
        );
    }
    
    public function getAllowedLocalesWithoutExcludeLocales()
    {
        $allowedLocales = $this->getAllowedLocales();
        $allowedLocales = array_diff($allowedLocales, array("en_ie"));
        
        return $allowedLocales;
    }
    
    public function getAllowedStoreViews()
    {
        return array(
            'fr_fr' => 1,
            'en_gb' => 2,
            //'en_ie' => 2,
            'ja_jp' => 2,
            'es_es' => 2,
            'pt_pt' => 2 
        );
    }

    public function getLocalizedFields()
    {
        return array(
            'title',
            'description',
            'ingredients_content',
            'ingredients_legend'
        );
    }

    public function getStoreLocales()
    {
        return array(
            1 => array('fr_fr'),
            2 => array('en_gb'),
            3 => array('en_gb'),
            4 => array('es_es'),
            5 => array('en_gb'),
            6 => array('en_gb'),
            7 => array('fr_fr'),
            8 => array('es_es'),
            9 => array('en_gb'),
            10 => array('en_gb')
        );
    }

    public function getDefaultLocaleForStoreId($storeId)
    {
        $locales = $this->getStoreLocales();
        return $locales[$storeId];
    }

    public function isLocaleAllowed($locale)
    {
        $locale = trim(strtolower($locale));
        return in_array($locale, $this->getAllowedLocales());
    }

    public function getCountryFromLocale($locale)
    {
        return substr($locale, -strrpos($locale, '_'));
    }

    public function getLangFromLocale($locale)
    {
        return substr($locale, 0, strpos($locale, '_'));
    }
    
    public function getDefaultLocaleForLang($lang)
    {
        switch($lang)
        {
            case 'fr':
                $result = 'fr_FR';
                break;
            case 'en':
                $result = 'en_GB';
                break;
            case 'pt':
                $result = 'pt_PT';
                break;
            case 'ja':
                $result = 'ja_JP';
                break;
            case 'es':
                $result = 'es_ES';
                break;
            default:
                $result = 'en_GB';
                break;
        }
        
        return $result;
    }
    
    public function getDefaultLocaleForLocalizedFieldByLocale($locale)
    {
        $locale = strtolower($locale);
        list($lang, $country) = explode('_', $locale);
        
        return strtolower($this->getDefaultLocaleForLang($lang));
    }
    
    public function getCurrentLocale()
    {
        $storeId = Mage::app()->getStore()->getId();
        return current($this->getDefaultLocaleForStoreId($storeId));
    }

    public function getTypes()
    {
        return array(
            Dbm_Share_Model_Element::TYPE_PHOTO,
            Dbm_Share_Model_Element::TYPE_RECEIPE
        );
    }

    public function isTypeAllowed($type)
    {
        $types = $this->getTypes();
        $types[] = Dbm_Share_Model_Element::TYPE_ALL;

        return in_array(strtolower(trim($type)), $types);
    }

    public function getPhotoUrl($filename)
    {
        $mageUrl = str_replace('index.php/', '', $this->getBaseUrl());
        return $mageUrl.'media/'.self::MAIN_MEDIA_FOLDER.'/'.Dbm_Share_Model_Photo::MEDIA_FOLDER.'/'.$this->getPhotoDir($filename, false, '/').rawurlencode($filename);
    }

    public function getPhotoDir($filename = null, $fullPath = true, $separator = DS)
    {
        if($fullPath)
        {
            $result = Mage::getBaseDir('media')
                .$separator.self::MAIN_MEDIA_FOLDER
                .$separator.Dbm_Share_Model_Photo::MEDIA_FOLDER.$separator;
        }
        else
        {
            $result = '';
        }

        if($filename)
        {
            $pointPos = strpos($filename, '.');
            $result .= substr($filename, $pointPos-6, 3).$separator.substr($filename, $pointPos-3, 3).$separator;
        }

        return $result;
    }

    /**
     * Delete empty folder from filename.
     * @param string $filename
     */
    public function cleanPhotoPath($filename)
    {
        $path = $this->getPhotoDir($filename);
        $file = new Varien_Io_File();
        $file->cd($path);
        $ls  = $file->ls();

        //Delete empty subfolder
        if(count($ls) == 0)
        {
            Varien_Io_File::rmdirRecursive($path);
        }

        $file->cd(dirname($path));
        $ls = $file->ls();

        //Delete empty root folder
        if(count($ls) == 0)
        {
            Varien_Io_File::rmdirRecursive(dirname($path));
        }
    }

    public function getCategoryImagePath($fullPath = null, $separator = DS)
    {
        $result = '';
        if($fullPath)
        {
            $result = Mage::getBaseDir('media').$separator;
        }

        $result .= self::MAIN_MEDIA_FOLDER.$separator.Dbm_Share_Model_Category::MEDIA_FOLDER.$separator;

        return $result;
    }

    /**
     * Extract wsg84 gps data from image full path.
     *
     * @param string $filePath
     * @return array
     */
    public function getGpsCoords($filePath)
    {
        Mage::log('FETCHING EXIF DATA : ');
        if(file_exists($filePath) && is_file($filePath))
        {
            $exifData = exif_read_data($filePath);
            
            if(isset($exifData['GPSLatitude'])
                && isset($exifData['GPSLatitudeRef'])
                && isset($exifData['GPSLongitude'])
                && isset($exifData['GPSLongitudeRef'])
            )
                return array(
                    'lat' => $this->_getGpsCoords($exifData["GPSLatitude"], $exifData['GPSLatitudeRef']),
                    'lng' => $this->_getGpsCoords($exifData["GPSLongitude"], $exifData['GPSLongitudeRef'])
            );
        }
    }

    /**
     * Perform gps calculation from exif data
     * Courtesy of http://stackoverflow.com/users/1060679/david
     *
     * @param float $coordinate
     * @param string $hemisphere
     * @return float
     */
    protected function _getGpsCoords($coordinate, $hemisphere)
    {
        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordinate[$i]);
            if (count($part) == 1) {
                $coordinate[$i] = $part[0];
            }
            else if (count($part) == 2) {
                $coordinate[$i] = floatval($part[0])/floatval($part[1]);
            }
            else {
                $coordinate[$i] = 0;
            }
        }

        list($degrees, $minutes, $seconds) = $coordinate;
        $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
        return $sign * ($degrees + $minutes/60 + $seconds/3600);
    }

    public function getGMapsData($lat, $lng)
    {
        $url = self::GMAPS_GEOCODE_URL.'?latlng=%s,%s&sensor=false';
        $callUrl = sprintf($url, $lat, $lng);
        $result = false;

        $jsonContent = file_get_contents($callUrl);

        if(strlen($jsonContent) > 0)
        {
            $jsonResult = Zend_Json::decode($jsonContent);
            $result = current($jsonResult['results']);
        }

        return $result;
    }

    public function getApiListPageSize()
    {
        return Dbm_Share_Helper_Data::API_LIST_PAGE_SIZE;
    }

    public function isCustomerAllowedToLike(Mage_Customer_Model_Customer $customer, Dbm_Share_Model_Element $element)
    {
        return $element->getId() > 0
            && $element->getIdCustomer() > 0;
            //Pour ne pas liker ses propres éléments : && $element->getIdCustomer() != $customer->getId();
    }

    public function getPrettyType($type)
    {
        $result = '';
        switch($type)
        {
            case Dbm_Share_Model_Element::TYPE_PHOTO:
                $result = 'photo';
                break;
            case Dbm_Share_Model_Element::TYPE_RECEIPE:
                $result = 'recette';
                break;
        }

        return $result;
    }

    public function getUploadMaxFilesize()
    {
        $result = array(
            (int)(ini_get('upload_max_filesize')) => ini_get('upload_max_filesize'),
            (int)(ini_get('post_max_size')) => ini_get('post_max_size'),
            (int)(ini_get('memory_limit')) => ini_get('memory_limit'),
        );

        ksort($result);

        return current($result);
    }

    public function getAllowedPriceValues()
    {
        return array(0, 1, 2, 3);
    }

    public function getAllowedLevelValues()
    {
        return array(0, 1, 2, 3);
    }
    
    public function getPrettyLevels()
    {       
        return array(
            0 => '',
            1 => $this->__('Easy'),
            2 => $this->__('Medium'),
            3 => $this->__('Difficult')
        );
    }
    
    public function getPrettyLevel($level)
    {
        $levels = $this->getPrettyLevels();
        return $levels[$level];
    }
    public function getPrettyPrices()
    {
        return array(
            0 => '',
            1 => $this->__('Cheap'),
            2 => $this->__('Average cost'),
            3 => $this->__('Expensive')
        );
    }
    
    public function getPrettyPrice($price)
    {
        $prices = $this->getPrettyPrices();
        return $prices[$price];
    }
    
    public function getTimeUnits()
    {
        return array(
            self::DURATION_MINUTES,
            self::DURATION_HOURS
        );
    }

    public function getTimeUnitsForSelect()
    {
        $result = array(
            array(
                'label' => $this->__('Choose a duration type'),
                'value' => 0
            ),
            array(
                'label'=> $this->__('minutes'),
                'value' => self::DURATION_MINUTES
            ),
            array(
                'label'=> $this->__('heures'),
                'value' => self::DURATION_HOURS
            ),
        );

        return $result;
    }
    
    public function getTimeUnitsForConfig()
    {
        $units = $this->getTimeUnits();
        
        return array(
            self::DURATION_MINUTES => $this->getPrettyTimeUnit(self::DURATION_MINUTES),
            self::DURATION_HOURS => $this->getPrettyTimeUnit(self::DURATION_HOURS)
        );
    }
    
    public function wsdlize($data, $asObject = false)
    {
        $result = null;

        if(is_array($data))
        {
            foreach($data as $key => $val)
            {
                if($asObject)
                {
                    $tmpArray = new stdClass();
                    $tmpArray->key = $key;
                    $tmpArray->value = $this->wsdlize($val, $asObject);
                }
                else
                {
                    $tmpArray = array(
                        'key' => $key,
                        'value' => $this->wsdlize($val, $asObject)
                    );
                }
                

                $result[] = $tmpArray;
            }
        }
        else $result = $data;

        return $result;
    }

    public function unWsdlize($array)
    {
        $result = array();
        if(is_array($array))
        {
            foreach($array as $data)
            {
                $result[$data->key] = $data->value;
            }
        }

        return $result;
    }
    
    public function cleanCommentString($comment)
    {
        return trim($comment);
    }

    public function generateRandomFilename($filename, $offset)
    {
        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        return microtime(true).'.'.$offset.'.'.$ext;
    }

    public function isMimeTypeAllowed($mime)
    {
        $mime = trim(strtolower($mime));

        return in_array($mime, $this->getAllowedMimeTypes());
    }

    public function getAllowedMimeTypes()
    {
        return array(
            'image/jpeg',
            'image/jpg',
            'image/png'
        );
    }
    
    public function getDefaultLayoutHandles(Mage_Core_Controller_Front_Action $controller)
    {
        return array('default', 'share_default', strtolower($controller->getFullActionName()));
    }

    public function getPrettyDate($dirtyDate)
    {
        $result = '';
        $date = Mage::app()->getLocale()->date();
        $date->set($dirtyDate, 'yyyy-MM-dd HH:mm:ss');
        $now = Mage::app()->getLocale()->date();

        $time = $date->getTimestamp();
        $nowTime = $now->getTimestamp();
        $difTime = $nowTime - $time;

        $difDate = $now->sub($date);

        $measure = new Zend_Measure_Time($difDate->toValue(), Zend_Measure_Time::SECOND);

        $limits = array(
            array(
                'divide' => 1,
                'label_sing' => $this->__('%s second ago'),
                'label_plur' => $this->__('%s seconds ago'),
                'limit' => 60
            ),
            array(
                'divide' => 60,
                'label_sing' => $this->__('%s minute ago'),
                'label_plur' => $this->__('%s minutes ago'),
                'limit' => 60
            ),
            array(
                'divide' => 3600,
                'label_sing' => $this->__('%s hour ago'),
                'label_plur' => $this->__('%s hours ago'),
                'limit' => 24
            ),
            array(
                'divide' => (3600*24),
                'label_sing' => $this->__('%s day ago'),
                'label_plur' => $this->__('%s days ago'),
                'limit' => 31
            ),
            array(
                'label_sing' => $this->__('%s month ago'),
                'label_plur' => $this->__('%s months ago'),
                'limit' => 2628600*12,
                'measure' => $measure->getValue(),
                'convert' => Zend_Measure_Time::MONTH
            ),
            array(
                'label_sing' => $this->__('%s year ago'),
                'label_plur' => $this->__('%s years ago'),
                'measure' => $measure->getValue(),
                'convert' => Zend_Measure_Time::YEAR
            ),
        );

        foreach($limits as $limit)
        {
            if($limit['measure'])
            {
                $num = $limit['measure'];
            }
            else
            {
                $num = floor($difTime / $limit['divide']);
            }

            if($num < $limit['limit'] || !isset($limit['limit']))
            {

                if($limit['measure'])
                {
                    $measure->convertTo($limit['convert']);
                    $num = floor($measure->getValue());
                }

                if($num > 1)
                {
                    $label = $limit['label_plur'];
                }
                else
                {
                    $label = $limit['label_sing'];
                }

                $result = sprintf($label, intval($num));
                break;
            }
        }

        return $result;
    }

    public function getPrettyTimeUnit($code)
    {
        switch($code)
        {
            case self::DURATION_HOURS:
                $result = 'h';
                break;
            default:
                $result = 'min';
                break;
        }

        return $result;
    }
    
    public function ulLize($text)
    {
        $html = "";
        $explode = explode("\n", $text);
        
        foreach ($explode as $key => $value) {
            $value = trim($value);
            $class = strlen($value) ? 'point' : 'no-point';
            $html.= '<li class="'.$class.'">'.$value.'</li>';
        }
        return $html;
    }

    public function getAjaxLikeUrl()
    {
        return Mage::getUrl('club/ajax/like');
    }

    public function getPostElementUrl()
    {
        return Mage::getUrl('club/element/post');
    }
    

    public function getReceiptTranslateUrl()
    {
        return Mage::getUrl('club/ajax/getreceipttranslateform');
    }
    
    public function getGMapsString($lat, $lng)
    {
        $url = sprintf(self::GMAPS_GEOCODE_REVERSE_URL, $lat, $lng);
        $json = file_get_contents($url);
        
        $response = Zend_Json::decode($json);
        
        foreach($response['results'] as $responsePart)
        {
            if(is_array($responsePart['address_components']))
            {
                foreach($responsePart['address_components'] as $component)
                {
                    if(is_array($component['types']))
                    {
                        if(in_array('locality', $component['types']) && in_array('political', $component['types']))
                        {
                            $result[0] = $component['long_name'];
                        }
                        
                        if(in_array('country', $component['types']) && in_array('political', $component['types']))
                        {
                            $result[1] = $component['long_name'];
                        }
                    }
                }
            }
            
            if(count($result) == 2)
            {
                break;
            }
        }
        
        $result = trim(implode(' / ', $result));
        
        /*
        if(count($response['results']) > 3)
        {
            $part = $response['results'][3];
        }
        elseif(count($response['results']) > 2)
        {
            $part = $response['results'][2];
        }
        elseif(count($response['results']) > 1) 
        {
            $part = $response['results'][1];
        }
        else
        {
            $part = $response['results'][0];
        }
        
        
        $result = $part['formatted_address'];
         */
        
        return $result;
    }
    
    public function getBaseUrl()
    {
        return $mageUrl = str_replace('index.php/', '', Mage::getUrl());
    }
    
    public function getAbuseElementUrl($idElement)
    {
        return Mage::getUrl('club/element/abuseElement', array('id' => intval($idElement)));
    }
    
    public function getAbuseCommentUrl($idElement)
    {
        return Mage::getUrl('club/element/abuseComment', array('id' => intval($idElement)));
    }
    
    public function getDeleteUrl($idElement)
    {
        return Mage::getUrl('club/element/delete', array('id' => intval($idElement)));
    }
    
    public function getDefaultReceipeImageUrl($size, $options)
    {
        $default = Mage::getDesign()->getSkinUrl('images/club/element/default/receipe.jpg');
        $helper = Mage::helper('dbm_utils/image');

        return $helper->resizeImage($default, $size[0], $size[1], $options);
    }
    
    public function startLocale($locale, $areas = 'frontend')
    {
        $locale = $this->cleanLocale($locale);
        
        if(!is_array($areas))
        {
            $areas = array($areas);
        }
        
        $currentLocale = Mage::app()->getLocale()->getLocaleCode();
        
        Mage::getSingleton('core/session')->setTempLocale($currentLocale);
        
        Mage::app()->getLocale()->setLocale($locale);
        Mage::app()->getTranslator()->setLocale($locale);
        
        foreach($areas as $area)
        {
            Mage::app()->getTranslator()->init($area, true);
        }
    }
    
    public function endLocale($areas = 'frontend')
    {
        if(!is_array($areas))
        {
            $areas = array($areas);
        }
        
        $oldLocale = Mage::getSingleton('core/session')->getTempLocale();
        $oldLocale = $this->cleanLocale($locale);
        
        Mage::getSingleton('core/session')->unsetData('temp_locale');
        Mage::app()->getLocale()->setLocale($oldLocale);
        Mage::app()->getTranslator()->setLocale($oldLocale);
        
        foreach($areas as $area)
        {
            Mage::app()->getTranslator()->init($area, true);
        }
    }
    
    public function cleanLocale($locale)
    {
        list($lang, $country) = explode('_', $locale);
        
        if($country)
        {
            $lang = strtolower($lang);
            $country = strtoupper($country);
            $locale = $lang.'_'.$country;
        }
        
        return $locale;
    }
    
    public function setTopMenuAlias($url)
    {
        $url = Mage::getUrl($url);
        Mage::register(self::REG_MENU_TOP, $url, true);
    }
    
    public function setMainMenuAlias($url)
    {
        $url = Mage::getUrl($url);
        Mage::register(self::REG_MENU_MAIN, $url, true);
    }
    
    public function getLoggedInClass()
    {
        return Mage::helper('dbm_customer')->getCurrentCustomer()->getId() ? self::LOGGED_IN_CLASS : self::NOT_LOGGED_IN_CLASS;
    }
    
    public function getCountOrderPositionByCustomerAndOrder($customer, $order = null)
    {
        $orderCollection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('state', array('in' => array('complete', 'processing', 'closed')))
                ->setOrder('created_at', 'desc');
        $totalCount = $orderCollection->getSize();
        $tempCount = $totalCount;
        
        if(!is_null($order))
        {
            if ($totalCount > 0) {
                foreach ($orderCollection as $orderC) {
                    if ($orderC->getId() != $order->getId()) {
                        $tempCount--;
                    } else {
                        return $tempCount;
                    }
                }
            }
        }
        
        return $tempCount;
    }
}
