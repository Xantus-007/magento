<?php

class Dbm_Country_Adminhtml_CmsController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        
        $this->_publicActions[] = 'duplicateUs';
        $this->_publicActions[] = 'copyBlocks';
         
        parent::_construct();
    }
    
    public function duplicateUsAction()
    {
        //Languages to duplicate
        $langs = array('en');
        $helper = Mage::helper('dbm_country');
        $config = $helper->getAllowedStocks();
        
        foreach($langs as $lang)
        {
            $this->_copyStore($config['us']['prefix'].$lang, $config['hk']['prefix'].$lang);
        }
        
        echo '<pre>END</pre>';
        exit();
    }
    
    protected function _copyStore($fromCode, $toCode)
    {
        echo '<pre>COPYING FROM '.$fromCode.' TO : '.$toCode.'</pre>';
        $helper = Mage::helper('dbm_country');
        
        $fromStore = Mage::getModel('core/store')->load($fromCode);
        $toStore = Mage::getModel('core/store')->load($toCode);
        $added = array();
        
        if($toStore->getId() && $fromStore->getId())
        {
            $collection = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter($fromStore->getId())
            ;
            
            foreach($collection as $page)
            {
                $addedPage = $this->_copyPage($page, $toStore);
                
                if($addedPage && $addedPage->getId())
                {
                    $added[] = $addedPage;
                }
            }
            
            foreach($added as $page)
            {
                $this->_cleanParents($page, $toStore);
            }
        }
    }
    
    protected function _copyPage($page, Mage_Core_Model_Store $toStore)
    {
        $result = null;
        $unsetFields = array('page_id', 'creation_time', 'update_time');
        $pageData = $page->getData();
        
        $currentStores = $page->getResource()->lookupStoreIds($page->getId());
        
        foreach($unsetFields as $field)
        {
            unset($pageData[$field]);
        }
        
        $pageData['stores'] = array($toStore->getId());
        
        echo '<pre>Copying page '.$page->getId().'['.$page->getIdentifier().'] to '.$toStore->getCode().'</pre>';
        
        if(!in_array(0, $currentStores))
        {
            $pageCollection = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter($toStore)
                ->addFieldToFilter('identifier', $page->getIdentifier());
            
            
            if(count($pageCollection) == 0)
            {
                $saveModel = Mage::getModel('cms/page');
                $saveModel->setData($pageData);
                $saveModel->save();
                $result = $saveModel;
            }
            else
            {
                echo '<pre>PAGE ALREADY EXISTS : '.$page->getId().' '.$page->getIdentifier().' ON STORES :  '.print_r($currentStores, true).'</pre>';
            }
        }
        else
        {
            echo '<pre>ERROR : '.$page->getId().'</pre>';
        }
        
        return $result;
    }
    
    protected function _cleanParents(Mage_Cms_Model_Page $page, $toStore)
    {
        $oldParent = $page->getParent();
        
        if($oldParent > 0)
        {
            $parent = Mage::getModel('cms/page')->load($oldParent);
            echo '<pre>UPDATING PAGE '.$page->getTitle().' to '.$parent->getId().'</pre>';
            $newCollection = Mage::getModel('cms/page')->getCollection()
                ->addFieldToFilter('identifier', $parent->getIdentifier())
                ->addStoreFilter($toStore)
            ;
            
            if(count($newCollection))
            {
                $newParent = $newCollection->getFirstItem();
                $page->setParent($newParent->getId());
                //echo '<pre>'.print_r($page->getData(), true).'</pre>';
                $page->save();
            }
        }
    }
    
    public function copyBlocksAction()
    {
        $copyTo = array('en');
        $unsetFields = array('block_id', 'creation_time', 'update_time');
        
        foreach($copyTo as $lang)
        {
            echo '<pre>SEARCHING FOR STORE : '.$lang.'</pre>';
            
            $fromStore = Mage::getModel('core/store')->load($lang, 'code');
            $toStore = Mage::getModel('core/store')->load('hk_'.$lang, 'code');
            $blocks = Mage::getModel('cms/block')->getCollection()
                ->addStoreFilter($fromStore)
            ;
            
            foreach($blocks as $block)
            {
                echo '<pre>COPYING BLOCK : '.$block->getId().' '.$block->getName().'</pre>';
                
                $testCollection = Mage::getModel('cms/block')->getCollection()
                    ->addStoreFilter($toStore)
                    ->addFieldToFilter('identifier', $block->getData())
                ;
                
                if(!count($testCollection))
                {
                    echo '<pre>OK : '.$block->getId().' - '.$block->getIdentifier().'</pre>';
                    
                    $blockData = $block->getData();
                    
                    foreach($unsetFields as $field)
                    {
                        unset($blockData[$field]);
                    }
                    
                    $blockData['stores'] = array($toStore->getId());
                    
                    $newBlock = Mage::getModel('cms/block');
                    $newBlock->setData($blockData);
                    $newBlock->save();
                }
                else
                {
                    echo '<pre>NOK '.$block->getId().' - '.$block->getIdentifier().'</pre>';
                }
                
                
            }
        }
        
        echo '<pre>'.count($blocks).'</pre>';
        exit();
    }
}
