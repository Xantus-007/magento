<?php

class Dbm_Share_Block_Menu_News extends Dbm_Share_Block_Menu_Abstract
{
    
    public function getLinks()
    {
        $cats = $this->_getCats();
        $result = array();
        
        foreach($cats as $cat)
        {
            $class = '';
            $url = $this->getUrl('blog/cat/'.$cat->getIdentifier());
            if(!empty($cat) && $cat == $requestCat)
            {
                $class = 'selected';
            }
            
            $result[$url] = array(
                'label' => $cat->getTitle(),
                'class' => $class
            );
        }

        return $result;
    }
    
    public function isCurrentUrl($url)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $requestCat = $request->getParam('cat');
        $this->getRequest()->getParam('identifier', $this->getRequest()->getParam('id', false));
        
        //Load blog post
        $identifier = $this->getRequest()->getParam('identifier', $this->getRequest()->getParam('id', false));
        $post = Mage::getModel('blog/post')->load($identifier, 'identifier');
        
        //$postId = $request->
        if($post->getId() > 0)
        {
            $cats = Mage::getModel('blog/cat')->getCollection()->addPostFilter($post->getId());
            
            foreach($cats as $cat)
            {
                $testUrl = $this->getUrl('blog/cat/'.$cat->getIdentifier());
                
                if($testUrl == $url)
                {
                    return true;
                }
            }
        }
        elseif(!empty($requestCat))
        {
            $cats = $this->_getCats();

            foreach($cats as $cat)
            {
                $testUrl = $this->getUrl('blog/cat/'.$requestCat);

                if($url == $testUrl)
                {
                    return true;
                }
            }

            $result = false;
        }
        else
        {
            $result = parent::isCurrentUrl($url);
        }
        
        return $result;
    }
    
    protected function _getCats()
    {
        return Mage::getModel('blog/cat')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addOrder('sort_order', 'ASC');
    }
}