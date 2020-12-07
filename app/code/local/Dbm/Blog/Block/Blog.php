<?php

class Dbm_Blog_Block_Blog extends AW_Blog_Block_Blog
{
    public function getPosts()
    {
        $tag = $this->getRequest()->getParam('tag');

        $collection = Mage::getModel('blog/blog')->getCollection()
                ->addPresentFilter()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setOrder('created_time ', 'desc');

        if($tag)
        {
            $collection->addTagFilter(urldecode($tag));
        }

        $page = $this->getRequest()->getParam('page');


        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

        $collection->setPageSize((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PERPAGE));
        $collection->setCurPage($page);



        $route = Mage::helper('blog')->getRoute();

        foreach($collection as $item)
        {
            $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));

            $locale = Mage::app()->getLocale()->getLocale();
            
            $_ct = Mage::app()->getLocale()->date();
            $_ct->setLocale($locale);
            $_ct->setDate($item->getCreatedTime(), 'yyyy-MM-dd HH:mm:ss');
            
            $item->setPrettyDate($_ct->toString('dd MMM yyyy'));
            $item->setCreatedTime($this->formatTime($item->getCreatedTime(), Mage::getStoreConfig('blog/blog/dateformat'), true));
            $item->setUpdateTime($this->formatTime($item->getUpdateTime(), Mage::getStoreConfig('blog/blog/dateformat'), true));
            
            if(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_USESHORTCONTENT) && strip_tags(trim($item->getShortContent())))
            {
                $content = trim($item->getShortContent());
                //$content = $this->closetags($content);
                $content .= ' <a class="read-more" href="' . $this->getUrl($route . "/" . $item->getIdentifier()) . '" >' . $this->__('Read More') . '</a>';
                $item->setPostContent($content);
            }
            elseif((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE) != 0)
            {
                $content = $item->getPostContent();
                if(strlen($content) >= (int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE))
                {
                    $content = substr($content, 0, (int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE));
                    $content = substr($content, 0, strrpos($content, ' '));
                    //$content = $this->closetags($content);
                    $content .= ' <a class="read-more" href="' . $this->getUrl($route . "/" . $item->getIdentifier()) . '" >' . $this->__('Read More') . '</a>';
                }
                $item->setPostContent($content);
            }


            $comments = Mage::getModel('blog/comment')->getCollection()
                    ->addPostFilter($item->getPostId())
                    ->addApproveFilter(2);
            $item->setCommentCount(count($comments));

            $cats = Mage::getModel('blog/cat')->getCollection()
                    ->addPostFilter($item->getPostId());
            $catUrls = array();
            foreach($cats as $cat)
            {
                $catUrls[$cat->getTitle()] = Mage::getUrl($route . "/cat/" . $cat->getIdentifier());
            }
            $item->setCats($catUrls);
        }
        return $collection;
    }

    public function getBookmarkHtml($post)
    {
        if(Mage::getStoreConfig('blog/blog/bookmarkslist'))
        {
            $this->setTemplate('aw_blog/bookmark.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

    public function getTagsHtml($post)
    {
        if(trim($post->getTags()))
        {
            $this->setTemplate('aw_blog/line_tags.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

}
