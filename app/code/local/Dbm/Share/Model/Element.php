<?php

class Dbm_Share_Model_Element extends Dbm_Share_Model_Timelogged_Abstract
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_PHOTO = 'photo';
    const TYPE_RECEIPE = 'receipe';
    const TYPE_ALL = 'all';
    
    const ALLOWED_PHOTOS = 1;
    
    public function _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_share/element', 'dbm_share/element_collection');
    }

    public function flushPhotos()
    {
        if($this->getId() > 0)
        {
            foreach($this->getPhotos() as $photo)
            {
                $photo->delete();
            }
        }
    }

    public function getPhotos()
    {
        return Mage::getModel('dbm_share/photo')->getCollection()->addElementFilter($this);
    }

    public function saveCategoryIds($catIds)
    {
        if($this->getId() > 0)
        {
            $this->getResource()->saveCategoryIds($this->getId(), $catIds);
        }

        return $this;
    }

    public function getCategories()
    {
        return Mage::getModel('dbm_share/category')->getCollection()->addElementFilter($this);
    }

    public function toApiArray()
    {
        return $this->getResource()->toApiArray($this);
    }

    public function like(Mage_Customer_Model_Customer $customer)
    {
        if(Mage::helper('dbm_share')->isCustomerAllowedToLike($customer, $this))
        {
            $this->getResource()->toggleLike($customer, $this, true);
        }
    }

    public function unlike(Mage_Customer_Model_Customer $customer)
    {
        if(Mage::helper('dbm_share')->isCustomerAllowedToLike($customer, $this))
        {
            $this->getResource()->toggleLike($customer, $this, false);
        }
    }

    public function getLikeCount()
    {
        $result = 0;

        if($this->getId())
        {
            $collection = Mage::getModel('dbm_share/like')->getCollection()
                ->addFieldToFilter('id_element', $this->getId());
            $result = $collection->count();
        }

        return $result;
    }

    public function isLikedBy(Mage_Customer_Model_Customer $customer)
    {
        $result = false;

        if($this->getId())
        {
            $collection = Mage::getModel('dbm_share/like')->getCollection()
                ->addFieldToFilter('id_customer', $customer->getId())
                ->addFieldToFilter('id_element', $this->getId())
            ;

            $result = $collection->count() > 0;
        }
        
        return $result;
    }
    
    public function getComments($filterAllowed = true)
    {
        $collection = Mage::getModel('dbm_share/comment')->getCollection()
            ->addFieldToFilter('id_element', $this->getId())
        ;
        
        if($filterAllowed)
        {
            $collection->addFieldToFilter('status', self::STATUS_ACTIVE);
        }
        
        return $collection;
    }
    
    public function saveElementFromApi($type, Mage_Customer_Model_Customer $customer, $data)
    {
        $this->_validateApiData($type, $data);
        $trans = Mage::helper('dbm_share');
        $helper = Mage::helper('dbm_share');
        
        if(!$customer->getId())
        {
            $this->_apiError($trans->__('Unknown user'));
        }
        
        if(!Mage::helper('dbm_customer')->isValidProfile($customer))
        {
            $this->_apiError($trans->__('You must complete your profile before posting'));
        }
        
        $photoSaveData = array();
        $rollBack = array();
        
        //Move Images
        for($i = 0; $i < self::ALLOWED_PHOTOS; $i++)
        {
            if(isset($data->photos[$i]))
            {
                $photo = $data->photos[$i];
                $filename = $helper->generateRandomFilename($photo->filename, $i);
                $filePath = $helper->getPhotoDir($filename);
                $fullPath = $filePath.$filename;

                $io = new Varien_Io_File();
                $io->setAllowCreateFolders(true);
                $io->createDestinationDir($filePath);

                file_put_contents($fullPath, base64_decode($photo->data));

                //Test if image is jpg/png
                $fp = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($fp, $fullPath);
                finfo_close($fp);

                if(!$helper->isMimeTypeAllowed($mime))
                {
                    $rollBack[] = array(
                        'filename' => $filename,
                        'fullPath' => $fullPath
                    );
                }
                else
                {
                    $io = new Varien_Io_File();
                    $fileExt = strtolower(substr($filename, strrpos($filename, '.') + 1));
                    $mimeExt = strtolower(trim(next(explode('/', $mime))));

                    if((!$helper->isMimeTypeAllowed('image/'.$fileExt) && $helper->isMimeTypeAllowed('image/'.$mimeExt)) || $mimeExt != $fileExt)
                    {
                        $newFilename = $helper->generateRandomFilename($photo->filename.'.'.$mimeExt, $i);
                        $newFilePath = $helper->getPhotoDir($newFilename);

                        if(strlen($newFilename))
                        {
                            rename($fullPath, $newFilePath.$newFilename);
                            $filename = $newFilename;
                            $filePath = $newFilePath;
                            $fullPath =  $filePath.$filename;
                        }
                    }
                }

                $md5 = md5_file($fullPath);
                $coords = $helper->getGpsCoords($fullPath);

                if(is_object($photo->coords) && (!$coords || (is_array($coords) && (!$coords['lat'] || !$coords['lng']))))
                {
                    if(strlen($photo->coords->lat) && $photo->coords->lng)
                    {
                        $coords = array(
                            'lat'  => $photo->coords->lat,
                            'lng'  => $photo->coords->lng
                        );
                    }
                }

                //Saving photo element
                $tmpPhotoSaveData = array(
                    'fullPath' => $fullPath,
                    'filename' => $filename,
                    'gmaps_label' => $photo->gmaps_label,
                    'md5' => $md5
                );

                if(is_array($coords))
                {
                    $tmpPhotoSaveData['lat'] = $coords['lat'];
                    $tmpPhotoSaveData['lng'] = $coords['lng'];

                    //Fetch Data from gmaps :
                    $tmpPhotoSaveData['gmaps_label'] = Mage::helper('dbm_share')->getGMapsString($coords['lat'], $coords['lng']);
                }

                $photoSaveData[] = $tmpPhotoSaveData;
            }
        }
        
        if(count($rollBack))
        {
            foreach($photoSaveData as $roll)
            {
                @unlink($roll['fullPath']);
                $helper->cleanPhotoPath($roll['filename']);
            }
            
            $this->_apiError($trans->__('Image type is not allowed'));
        }
        
        //Save Data
        $saveData = array(
            //'title_'.$locale => $data->title,
            'id_customer' => $customer->getId(),
            'type' => $type
        );
        
        //TestingCategories
        $finalCats = array();
        if(is_array($data->categories))
        {
            foreach($data->categories as $catId)
            {
                if(is_array($catId)) //Patch. La version multi cat√©gorie marchais sur les applis mais pas sur le web
                {
                    foreach ($catId as $cid) {
                        $testCat = Mage::getModel('dbm_share/category')->load($cid);
                        if($testCat->getId() > 0)
                        {
                            $finalCats[] = $testCat->getId();
                        }
                    }
                }else{
                    $testCat = Mage::getModel('dbm_share/category')->load($catId);
                    if($testCat->getId() > 0)
                    {
                        $finalCats[] = $testCat->getId();
                    }
                }
            }
        }
        
        if($type == self::TYPE_RECEIPE)
        {
            $localisedFields = Mage::helper('dbm_share')->getLocalizedFields();
            
            $saveData += array(
                'level' => $data->level,
                'price' => $data->price,
                'duration' => $data->duration,
                'duration_unit' => $data->duration_unit,
                'cooking_duration' => $data->cooking_duration,
                'cooking_duration_unit' => $data->cooking_duration_unit
            );
            
        }
        elseif($type == self::TYPE_PHOTO)
        {
            $localisedFields = array('title');
        }
        
        foreach($localisedFields as $field)
        {
            if(is_array($data->{$field}))
            {
                foreach($data->{$field} as $localisedData)
                {
                    $tempData = $localisedData->value;
                    
                    if($field == 'ingredients_legend')
                    {
                        $tempData = intval($tempData);
                    }
                    
                    $locale = strtolower($localisedData->key);
                    $defaultLocale = $helper->getDefaultLocaleForLocalizedFieldByLocale($locale);
                    $saveData[$field.'_'.$defaultLocale] = $tempData;
                }
            }
        }
        
        $element = Mage::getModel('dbm_share/element');
        $element->setData($saveData);
        
        if($element->save())
        {
            Mage::dispatchEvent(Dbm_Share_Helper_Data::EVENT_ADD, array('element' => $element));
            $result = $element->getId();
        }

        //Save Categories
        if($element->getId() > 0)
        {
            $element->saveCategoryIds($finalCats);
            
            //Save photos
            foreach($photoSaveData as $tmpPhotoData)
            {
                $tmpPhotoData['id_element'] = $element->getId();

                $photo = Mage::getModel('dbm_share/photo');
                $photo->setData($tmpPhotoData);
                $photo->save();
            }
        }
        
        return $result;
    }
    
    public function apiDelete(Mage_Customer_Model_Customer $customer)
    {
        $trans = Mage::helper('dbm_share');
        if($this->getId() > 0 && $this->getIdCustomer() == $customer->getId())
        {
            Mage::dispatchEvent(Dbm_Share_Helper_Data::EVENT_REMOVE, array('element' => $this));
            $this->delete();
        }
        else
        {
            $this->_apiError($trans->__('You are not allowed to delete this element'));
        }
    }

    public function getCustomer()
    {
        if($this->getId())
        { 
            return Mage::getModel('dbm_customer/customer')->load($this->getIdCustomer());
        }
    }

    public function getLevelImage()
    {
        return Mage::getDesign()->getSkinUrl('images/svg/clubento/level_'.intval($this->getLevel()).'.svg');
    }

    public function getPriceImage()
    {
        return Mage::getDesign()->getSkinUrl('images/svg/clubento/price_'.intval($this->getPrice()).'.svg');
    }

    public function getLikeImage(Mage_Customer_Model_Customer $customer)
    {
        $isLiked = intval($this->isLikedBy($customer));
        return Mage::getDesign()->getSkinUrl('images/svg/clubento/like_'.$isLiked.'.svg');
    }

    public function getLink()
    {
        $result = null;
        
        if($this->getId())
        {
            //return Mage::helper('dbm_share')->getBaseUrl().
            $result = str_replace('index.php/', '', Mage::getUrl('club/index/detail/', array('id' => $this->getId())));
        }
        
        return $result;
    }
    
    public function abuse()
    {
        return $this->getResource()->abuse(Dbm_Share_Helper_Abuse::TYPE_ELEMENT, $this);
    }
    
    protected function _validateApiData($type, $data)
    {
        $trans = Mage::helper('dbm_share');
        if((!is_array($data->photos) || count($data->photos) == 0) && $type != self::TYPE_RECEIPE)
        {
            $this->_apiError($trans->__('No image was sent'));
        }
        
        $required = array(
            'title'
        );
        
        if($type == self::TYPE_RECEIPE)
        {
            $required += array(
                'description',
                //'ingredients_content',
                //'ingredients_legend',
                //'duration',
                //'duration_unit',
                //'cooking_duration',
                //'cooking_duration_unit',
                //'level',
                //'price'
            );
        }
        
        foreach($required as $field)
        {
            if(!count($data->{$field}))
            {
                $this->_apiError('Le champ '.$field.' est vide');
            }
        }
    }
    
    protected function _apiError($message)
    {
        Mage::throwException($message);
    }
    
    
    public function __call($name, $args)
    {
        $trans = Mage::helper('dbm_share');
        $result = parent::__call($name, $args);
        
        if($name == 'getIngredientsLegend')
        {
            $result = intval($result);
            if($result > 1)
            {
                $result = $trans->__('%d persons', $result);
            }
            elseif($result == 1)
            {
                $result = $trans->__('%d person', $result);
            }
            else
            {
                $result = '';
            }
        }
        
        return $result;
    }
}
