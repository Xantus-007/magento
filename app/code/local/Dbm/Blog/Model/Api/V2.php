<?php

class Dbm_Blog_Model_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
    public function getTree($storeView)
    {
        $result = array();
        $cats = $this->getCategories($storeView);
        
        foreach($cats as $cat)
        {
            $tmpCat = $cat;
            
            $tmpCat['posts'] = array();
            $posts = $this->getPosts($storeView, $cat['id']);
            
            foreach($posts as $post)
            {
                $realPost = $this->getPost($storeView, $post['id']);
                $tmpCat['posts'][] = $realPost;
            }
            
            $result[] = $tmpCat;
        }
        
        return $result;
    }
    
    public function getCategories($storeView)
    {
        $result = array();
        $this->_setStoreId($storeView);
        
        $cats = Mage::getModel('blog/cat')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addOrder('sort_order', 'ASC')
        ;
        
        foreach($cats as $cat)
        {
            $result[] = array(
                'id' => $cat->getId(),
                'title' => $cat->getTitle(),
                'description' => $cat->getMetaDescription()
            );
        }
        
        return $result;
    }
    
    public function getPosts($storeId, $categoryId)
    {
        $result = array();
        $this->_setStoreId($storeId);
        $cat = Mage::getModel('blog/cat')->load($categoryId);
        
        if($cat->getId())
        {
            $posts = $this->_getPostsCollection($storeId);
            $posts->addCatFilter($cat->getId());
            
            $result = $this->_postsListToApi($posts);
        }
        
        return $result;
    }
    
    public function getPost($storeView, $postId)
    {
        $result = array();
        $this->_setStoreId($storeView);
        
        $post = Mage::getModel('blog/post')->load($postId);
        
        if($post->getId())
        {
            $result['id'] = $post->getId();
            $result['title'] = $post->getTitle();
            $result['content'] = str_replace('<p>&nbsp;</p>', '', '<h1>'.$post->getTitle().'</h1>'.$post->getPostContent());
            $result['author'] = $post->getUser();
            $result['date'] = $post->getCreatedTime();
        }
        
        return $result;
    }
    
    public function getFlatPosts($storeId, $page = 0)
    {
        $collection = $this->_getPostsCollection($storeId, $page);
        $result = $this->_postsToApi($collection);
        return $result;
    }
    
    protected function _setStoreId($storeId)
    {
        Mage::app()->setCurrentStore($this->_getStoreId($storeId));
    }

    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault(Mage::helper('dbm_share')->__('Store does not exist'));
        }

        return $storeId;
    }
    
    protected function _getPostsCollection($storeId, $page = 0)
    {
        $collection = Mage::getModel('blog/blog')->getCollection()
            ->addStoreFilter($storeId)
            ->addPresentFilter()
            ->setOrder('created_time', 'desc')
            ->setPageSize(Dbm_Share_Helper_Data::API_LIST_PAGE_SIZE)
            ->setCurPage($page)
        ;
        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
        
        return $collection;
    }
    
    protected function _postsListToApi($collection)
    {
        $result = array();
        
        foreach($collection as $post)
        {
            $result[] = array(
                'id' => $post->getId(),
                'title' => $post->getTitle()
            );
        }
        
        return $result;
    }
    
    protected function _postsToApi($collection)
    {
        $result = array();
        $options = Mage::helper('dbm_share/image')->getOptionsForList();
        $helper = Mage::helper('dbm_utils/image');
        $route = Mage::helper('blog')->getRoute();
        
        foreach($collection as $post)
        {
            
            $preg = '#img.*src="([^"]*)"#';
            
            /* FIX BLOG APPLI BUG */
            $contentForThumb = str_replace(array('{{media url="', '"}}'), array(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), ''), $post->getPostContent());
            
            preg_match_all($preg, $contentForThumb, $res,  PREG_SET_ORDER );
            $thumb = $res[0][1];
            
            /* FIX BLOG APPLI BUG */
            if(@getimagesize($thumb)) $thumb = str_replace('index.php/', '', $helper->resizeImage($thumb, 1230, 490, $options));
            
            $tmpResult = array(
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => str_replace('<p>&nbsp;</p>', '', '<h1>'.$post->getTitle().'</h1>'.$post->getPostContent()),
                'author' => $post->getUser(),
                'date' => $post->getCreatedTime(),
                'link' => str_replace('index.php/', '', Mage::getUrl($route.'/'.$post->getIdentifier()))
            );
            
            if(@getimagesize($thumb))
            {
                $tmpResult['thumb'] = $thumb;
            }
            
            $result[] = $tmpResult;
        }
        
        return $result;
    }
}