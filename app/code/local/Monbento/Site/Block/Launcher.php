<?php

class Monbento_Site_Block_Launcher extends Mage_Core_Block_Template 
{
    public function getBlocsPromotionnels()
    {
        $blocs = array();
        $type = $this->getData('type');
        
        $limit = ($type == 'cart' && Mage::helper('mobiledetect')->isMobile()) ? 2 : 4;

        for($i=1;$i<$limit;$i++)
        {
            if($id = Mage::getStoreConfig('monbento_admin_wysiwyg/monbento_config_blocs_'.$type.'/monbento_bloc_'.$type.'_'.$i)) {
                $blocs[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($id);
            }
        }

        return $blocs;
    }
    
}