<?php

class Dbm_Share_Block_Menu_Elements extends Dbm_Share_Block_Menu_Abstract
{
    public function getLinks()
    {
        $trans = Mage::helper('dbm_share');

        if($this->getElementType())
        {
            $nameType = ($this->getElementType() == 'photo' ? 'photos' : 'recipes');
            
            $result = array(
                $this->getUrl('club/'.$nameType.'/index') => array(
                    'label' => $trans->__('Popular'),
                    'image' => $this->getSkinUrl('images/club/menu/left/popular_0.png'),
                    'image_selected' => $this->getSkinUrl('images/club/menu/left/popular_1.png')
                ),
                $this->getUrl('club/'.$nameType.'/index', array('category' => 'all')) => array(
                    'label' => $trans->__('All'),
                    'image' => $this->getSkinUrl('images/svg/clubento/menu/all_0.svg'),
                    'image_selected' => $this->getSkinUrl('images/svg/clubento/menu/all_1.svg')
                )
            );
            
            $categories = Mage::getModel('dbm_share/category')->getCollection()
                ->addTypeFilter($this->getElementType());
            $baseUrl = Mage::getUrl('media').Mage::helper('dbm_share')->getCategoryImagePath(null, '/');
            $imHelper = Mage::helper('dbm_share/image');
            $uHelper = Mage::helper('dbm_utils/image');
            $options = $imHelper->getOptionsForCategory();
            $sizes = $imHelper->getSizes();
            $size = $sizes['cat_menu'];

            foreach($categories as $cat)
            {
                $imageUrl  = Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.'/'.Dbm_Share_Model_Category::MEDIA_FOLDER.'/'.$cat->getImage();
                //$resized = $uHelper->resizeMediaImage($imageUrl, $size[0], $size[1], $options);
                $resized2 = null;
                if($cat->getImage2())
                {
                    $tmpOptions = $options;
                    $tmpOptions['bgColor'] = array(138, 22, 105);
                    
                    $imageUrl2 = Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.'/'.Dbm_Share_Model_Category::MEDIA_FOLDER.'/'.$cat->getImage2();
                    //$resized2 = $uHelper->resizeMediaImage($imageUrl2, $size[0], $size[1], $tmpOptions);
                }
                
                $tmpResult = array(
                    'label' => $cat->getTitle(),
                    'image' => Mage::getUrl('media') . $imageUrl
                );
                
                if($imageUrl2)
                {
                    $tmpResult['image_selected'] = Mage::getUrl('media') . $imageUrl2;
                }

                $result[$this->getUrl('club/'.$nameType.'/index', array('category' => $cat->getId()))] = $tmpResult;
            }
            
            /*
            $result[$this->getUrl('club/index/map/type/'.$this->getElementType())] = array(  
                'label' => $trans->__('Carte')
            );
             */
            
            return $result;
        }
    }
}