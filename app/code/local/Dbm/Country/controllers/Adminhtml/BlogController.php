<?php

class Dbm_Country_Adminhtml_BlogController extends Mage_Adminhtml_Controller_Action
{
    public function _construct()
    {
        $this->_publicActions[] = 'publish';
        $this->_publicActions[] = 'publish2';
    }
    /*
    public function publishAction()
    {
        $toStores = array(
            'fr' => 'us_fr',
            'en' => 'us_en',
            'es' => 'us_es'
        );
        
        //Publish blog posts
        $this->_publishBlogPosts($toStores);
        
        //Publish categories
        $this->_publishBlogCats($toStores);
    }
    
    protected function _publishBlogPosts($toStores)
    {
        foreach($toStores as $from => $to)
        {
            $fromStore = Mage::getModel('core/store')->load($from, 'code');
            $posts = Mage::getModel('blog/post')->getCollection()
                ->addStoreFilter($fromStore)
            ;
            
            //$posts->getSelect()->columns('store_table.store_id');
            
            foreach($posts as $post)
            {
                $tmpPost = Mage::getModel('blog/post')->load($post->getId());
                $toStore = Mage::getModel('core/store')->load($to, 'code');
                $storeIds = $tmpPost->getStoreId();
                
                if(!in_array($toStore->getId(), $storeIds))
                {
                    echo '<pre>SAVING : '.$tmpPost->getId().'</pre>';
                    
                    $storeIds[] = $toStore->getId();
                    $tmpPost->setStores($storeIds);
                    $tmpPost->setCats($tmpPost->getCatId());
                    $tmpPost->save();
                }
            }
        }
        
        echo 'END';
        exit();
    }
    
    */
    
    public function publish2Action()
    {
        $storeCodes = array(
            'en' => array(
                'it', 'es', 'de', 'us_en', 'us_es', 'hk_en'
            ),
            'fr' => array(
                'us_fr'
            )
        );
        
        $storeIds = array();
        $store = Mage::getModel('core/store');
        
        foreach($storeCodes as $fromStore => $toStores)
        {
            $currentStore = Mage::getModel('core/store')->load($fromStore, 'code');
            $currentArray = array();
            
            foreach($toStores as $storeCode)
            {
                $currentArray[] = Mage::getModel('core/store')->load($storeCode, 'code')->getId();
            }
            
            $storeIds[$currentStore->getId()] = $currentArray;
        }
        
        $posts = Mage::getModel('blog/post')->getCollection();
        
        
        foreach($posts as $post)
        {
            $tmpPost = Mage::getModel('blog/post')->load($post->getId());
            $postStores = $tmpPost->getStoreId();
            
            foreach($postStores as $fromStore)
            {
                if(isset($storeIds[$fromStore]))
                {
                    $postStores = array_unique(array_merge($postStores, $storeIds[$fromStore]));
                    //echo '<pre>'.print_r($postStores, true).'</pre>';
                }
            }
            
            $tmpPost->setStores($postStores);
            $tmpPost->setCats($tmpPost->getCatId());
            $tmpPost->save();
        }
        
        echo '<pre>END</pre>';
        exit();            
    }
    
    protected function _publishBlogCats($to)
    {
        print_r($to);
        exit();
    }
}
