<?php
/**
 * Description of Grid
 *
 * @author dlote
 */
class Dbm_Permissions_Block_Adminhtml_Promo_Quote_Grid extends Mage_Adminhtml_Block_Promo_Quote_Grid{
    
    public function __construct(){
        parent::__construct();
    }
    
    protected function _prepareCollection()
    {
        
        $collection = Mage::getModel('salesrule/rule')
            ->getResourceCollection();        
        
        $allStores = array();
        foreach (Mage::app()->getStores() as $_eachStoreId => $val) 
        {
            $allStores[] = Mage::app()->getStore($_eachStoreId)->getId();
        }
        $storeIds = Mage::helper('aitpermissions/access')->getFilteredStoreIds($allStores); 
        
        if(count($storeIds) != count($allStores)){
            $websiteIds = array();
            foreach ($storeIds as $storeId) {
                if(!in_array($storeId, $websiteIds)){
                    $websiteIds[] = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
                }
            }
            
            $collection->addFieldToFilter('website_ids',
                array(
                    'in'    => $websiteIds,
                )
            );
        }    
        
        $this->setCollection($collection);
            
        return $this;
    }
}
