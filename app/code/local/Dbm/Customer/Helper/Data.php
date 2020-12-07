<?php

class Dbm_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MEDIA_FOLDER = 'customer';

    const SOCIAL_PLATEFORM_FACEBOOK = 'facebook';
    const SOCIAL_PLATEFORM_TWITTER = 'twitter';
    
    const DISCOUNT_ID_CHEF = 177;
    const DISCOUNT_ID_SECOND = 178;

    const POINTS_FIRST_ORDER = 10;
    
    const ATTRIBUTE_POINTS_OTHER = 'points_other';
    const ATTRIBUTE_POINTS_PHOTO = 'points_photo';
    const ATTRIBUTE_POINTS_RECEIPE = 'points_receipe';
    
    public function getCurrentCustomer()
    {
        $customer = Mage::helper('customer')->getCustomer();

        if($customer && $customer->getId())
        {
            $customer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
        }

        return $customer;
    }

    public function getCustomerImageUrl(Mage_Customer_Model_Customer $customer, $size, $options)
    {
        $photoUri = $customer->getProfileImage();
        $imHelper = Mage::helper('dbm_utils/image');
        if($photoUri)
        {
            $url = self::MEDIA_FOLDER.$customer->getProfileImage();
        }

        return $imHelper->resizeMediaImage($url, $size[0], $size[1], $options);
    }

    public function getProfileStatus($locale = null)
    {
        $helper = Mage::helper('dbm_share');
        
        if($locale)
        {
            Mage::helper('dbm_share')->startLocale($locale);
        }
        
        $result = array(
            0 => $helper->__('Taster'),
            1 => $helper->__('Apprentice'),
            2 => $helper->__('Cook'),
            3 => $helper->__('Chef')
        );
        
        if($locale)
        {
            Mage::helper('dbm_share')->endLocale();
        }
        
        return $result;
    }
    
    public function getPrettyStatus(Mage_Customer_Model_Customer $customer)
    {
        $status = $this->getProfileStatus();
        
        return $status[$customer->getProfileStatus()];
    }

    public function getProfileStatusForSelect()
    {
        $result = array();
        foreach($this->getProfileStatus() as $key => $val)
        {
            $result[] = array(
                'value' => $key,
                'label' => $val
            );
        }

        return $result;
    }
    
    public function getAjaxSubscribeUrl()
    {
        return Mage::getUrl('club-customer/ajax/subscribe');
    }

    public function updateCustomerStatus(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getId())
        {
            /*
            $points = Mage::helper('auguria_sponsorship')->getPoints($customer);
            $points = $points['accumulated'];
            */
            
            $points = $this->getCustomerPoints($customer);
            $oldStatus = $customer->getProfileStatus();
            $hasProfile = $this->isValidProfile($customer);
            
            if($points >= 400 && $points < 5000)
            {
                $newStatus = 2;
            }
            elseif($points >= 5000)
            {
                $newStatus = 3;
            }
            elseif($hasProfile)
            {
                $newStatus = 1;
            }
            else
            {
                $newStatus = 0;
            }

            if(!$hasProfile)
            {
                $newStatus = 0;
            }
            
            if($oldStatus > $newStatus /*&& $hasProfile*/)
            {
                $newStatus = $oldStatus;
            }
            
            if($oldStatus == 0 && $newStatus >= 1)
            {
                $customer->setData('accumulated_points', $points + 10);
                $this->addCustomerPoints($customer, self::ATTRIBUTE_POINTS_OTHER, 10, false);
            }
            
            $customer->setProfileStatus($newStatus);
            $customer->save();
        }
    }

    public function getCustomerPoints(Mage_Customer_Model_Customer $customer)
    {
        $result = 0;
        if($customer->getId())
        {
            $customer = Mage::getModel('customer/customer')->load($customer->getId());
            $sum = array(
                'points_photo',
                'points_receipe',
                'points_other',
            );
            
            foreach($sum as $attribute)
            {
                $points = $customer->getData($attribute);
                
                if(is_array($points))
                {
                    $points = $points[$attribute];
                }
                
                $result = $result + $points;
            }
        }
        
        return $result;
    }
    
    public function addCustomerPoints($customer, $attributeCode, $addPoints, $updateStatus = true)
    {
        if($customer->getId())
        {
            $tmpCustomer = Mage::getModel('customer/customer')->load($customer->getId());
            $points = $tmpCustomer->getData($attributeCode);
            
            $customer->setData($attributeCode, $points + $addPoints)->getResource()->saveAttribute($customer, $attributeCode);
            
            if($updateStatus)
            {
                $this->updateCustomerStatus($customer);
            }
        }
    }
    
    public function getNotifications(Mage_Customer_Model_Customer $customer, $markAsRead = false)
    {
        $manager = Mage::getModel('dbm_customer/notification_manager');
        
        if($customer->getId())
        {
            $this->getLikeNotifications($customer, $manager);
            $this->getFollowersNotifications($customer, $manager);
            $this->getCommentsNotifications($customer, $manager);
            
            if($markAsRead)
            {
                Mage::getModel('dbm_customer')->updateNotifications($customer);
            }
        }
        
        return $manager;
    }
    
    public function getLikeNotifications(Mage_Customer_Model_Customer $customer, Dbm_Customer_Model_Notification_Manager $manager)
    {
        $sqlDateFormat = $this->getSqlDateFormat();
        $startDate = $this->getNotificationStartDate($customer);
        $sqlStartDate = $startDate->toString($sqlDateFormat);
        $trans = Mage::helper('dbm_share');
        
        //Fetching Likes
        $likes = Mage::getModel('dbm_share/like')->getCollection();
        $likes->addFieldToFilter('main_table.created_at', array('from' => $sqlStartDate))
            ->addCustomerFilter($customer)
        ;

        foreach($likes as $like)
        {
            $source = Mage::getModel('customer/customer')->load($like->getIdCustomer());
            $element = Mage::getModel('dbm_share/element')->load($like->getIdElement());
            $tmpDate = clone $startDate;
            $tmpDate->set($element->getCreatedAt(), $sqlDateFormat);

            $data = $element->toApiArray();
            $photo = current($data['photos']);

            //@TODO: translate
            $notif = Mage::getModel('dbm_customer/notification');
            $notif->init(
                $tmpDate, 
                $source, 
                $trans->__('%s liked your %s', $customer->getProfileNickname(), $trans->__($element->getType())), 
                $photo['url']
            );

            $manager->addNotification($notif);
        }
    }
    
    public function getFollowersNotifications(Mage_Customer_Model_Customer $customer, Dbm_Customer_Model_Notification_Manager $manager)
    {
        $sqlDateFormat = $this->getSqlDateFormat();
        $startDate = $this->getNotificationStartDate($customer);
        $sqlStartDate = $startDate->toString($sqlDateFormat);
        $trans = Mage::helper('dbm_share');
        
        //Fetching followers
        $links = Mage::getModel('dbm_customer/link')->getCollection()
            ->addFieldToFilter('id_following', $customer->getId())
            ->addFieldToFilter('created_at', array('from' => $sqlStartDate))
        ;

        foreach($links as $link)
        {
            $follower = Mage::getModel('dbm_customer/customer')->load($link->getIdCustomer());
            $tmpDate = clone $startDate;
            $tmpDate->set($link->getCreatedAt(), $sqlDateFormat);
            $profileImage = Mage::getBaseUrl('media').Dbm_Customer_Helper_Data::MEDIA_FOLDER.$customer->getProfileImage();

            $notif = Mage::getModel('dbm_customer/notification');
            $notif->init(
                $tmpDate, 
                $follower, 
                $trans->__('%s is following you', $follower->getProfileNickname()), 
                $profileImage
            );

            $manager->addNotification($notif);
        }
    }
    
    public function getCommentsNotifications(Mage_Customer_Model_Customer $customer, Dbm_Customer_Model_Notification_Manager $manager)
    {
        $sqlDateFormat = $this->getSqlDateFormat();
        $startDate = $this->getNotificationStartDate($customer);
        $sqlStartDate = $startDate->toString($sqlDateFormat);
        $trans = Mage::helper('dbm_share');
        
        //Fetching comments
        $comments = Mage::getModel('dbm_share/comment')->getCollection()
            ->addCustomerFilter($customer, true)
            ->addFieldToFilter('main_table.created_at', array('from' => $sqlStartDate));
        ;

        foreach($comments as $commentData)
        {
            $tmpDate = clone $startDate;
            $tmpDate->set($commentData->getFirstUpdateDate());

            $element = Mage::getModel('dbm_share/element')->load($commentData->getIdElement());

            $data = $element->toApiArray();
            $photo = current($data['photos']);

            $notif = Mage::getModel('dbm_customer/notification');
            $notif->init(
                $tmpDate, 
                $element, 
                $trans->__('%s users commented your %s', $commentData->getElementCount(), $trans->__($element->getType())), 
                $photo['url']
            );

            $manager->addNotification($notif);
        }
    }
    
    public function getSqlDateFormat()
    {
        return 'yyyy-MM-dd HH:mm:ss';
    }
    
    public function getNotificationStartDate(Mage_Customer_Model_Customer $customer)
    {
        $startDate = Mage::app()->getLocale()->date();
        $sqlDateFormat = 'yyyy-MM-dd HH:mm:ss';
        
        $customer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
        
        if(!$customer->hasNotificationDate())
        {
            $startDate->set($customer->getCreatedAt(), $sqlDateFormat);;
        }
        else
        {
            $startDate->set($customer->getNotificationDate());
        }
        
        return $startDate;
    }
    
    public function isValidProfile(Mage_Customer_Model_Customer $customer)
    {
        $customer = Mage::getModel('customer/customer')->load($customer->getId());
        
        return strlen($customer->getProfileNickname()) && strlen($customer->getProfileImage());
    }
    
    public function fixProfileUrl($url) {
        if (substr($url, 0, 7) == 'http://') { return $url; }
        if (substr($url, 0, 8) == 'https://') { return $url; }
        return 'http://'. $url;
    }
    
    public function isCategoryAllowedForCurrentCustomer($catModel)
    {
        $cat = Mage::getModel('catalog/category')->load($catModel->getId());
        $result = false;
        $currentCustomer = $this->getCurrentCustomer();
        $isLoggedIn = $currentCustomer->getId();
        
        $currentStatus = !$isLoggedIn ? null : $currentCustomer->getProfileStatus();
        $allowedStatus = explode(',',$cat->getAllowedStatus());
        
        if(!count($allowedStatus) || !strlen($cat->getAllowedStatus()) ||  in_array(-1, $allowedStatus))
        {
            $result = true;
        }
        else
        {
            if(in_array($currentStatus, $allowedStatus))
            {
                $result = true;
            }
        }
        
        return $result;
    }
    
    public function generateCustomerProfileData(Mage_Customer_Model_Customer $customer)
    {
        $result = null;
        
        if($customer->getId())
        {
            $time = time();
            $placeholder = $this->getProfilePlaceholderPath();
            $ext = strtolower(substr($placeholder, strrpos($placeholder, '.')));
            $imageName = $time.'-'.$customer->getId().$ext;
            $relativeFolder = DS.$imageName[1].DS.$imageName[0].DS;
            $newFolder = Mage::getBaseDir('media').DS. Dbm_Customer_Helper_Data::MEDIA_FOLDER.$relativeFolder;
            
            $fileIo = new Varien_Io_File();
            $fileIo->checkAndCreateFolder($newFolder);
            
            copy($placeholder, $newFolder.$imageName);
            
            $result['profile_nickname'] = uc_words($customer->getFirstname());
            $result['profile_image'] = $relativeFolder.$imageName;
        }
        
        return $result;
    }

    public function generateCustomerCreateProfileData($customer)
    {
        $time = time();
        $placeholder = $this->getProfilePlaceholderPath();
        $ext = strtolower(substr($placeholder, strrpos($placeholder, '.')));
        $imageName = $time.'-placeholder'.$ext;
        $relativeFolder = DS.$imageName[1].DS.$imageName[0].DS;
        $newFolder = Mage::getBaseDir('media').DS. Dbm_Customer_Helper_Data::MEDIA_FOLDER.$relativeFolder;
            
        $fileIo = new Varien_Io_File();
        $fileIo->checkAndCreateFolder($newFolder);
            
        copy($placeholder, $newFolder.$imageName);
            
        $result['profile_nickname'] = uc_words($customer->getFirstname());
        $result['profile_image'] = $relativeFolder.$imageName;
        $result['accumulated_points'] = 10;
        $result['points_other'] = 10;
        $result['profile_status'] = 1;

        return $result;
    }
    
    public function getProfilePlaceholderPath()
    {
        $skinUrl = Mage::getDesign()->getSkinUrl('images/club/profile/placeholder.png');
        
        $baseUrl = Mage::getBaseUrl();
        $baseUrl = explode('/', $baseUrl);
        if(isset($baseUrl[3]))
        {
            unset($baseUrl[3]);
        }
        
        $baseUrl = implode('/', $baseUrl);
        
        $skinUrl = '/'.str_replace($baseUrl, '', $skinUrl);
        
        return Mage::getBaseDir().$skinUrl;
    }
}
