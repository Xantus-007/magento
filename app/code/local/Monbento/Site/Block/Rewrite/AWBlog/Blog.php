<?php

class Monbento_Site_Block_Rewrite_AWBlog_Blog extends AW_Blog_Block_Blog 
{

    public function getPosts() 
    {
        $tag = $this->getRequest()->getParam('tag');

        $collection = Mage::getModel('blog/blog')->getCollection()
                ->addPresentFilter()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setOrder('created_time ', 'desc');

        if ($tag) {
            $collection->addTagFilter(urldecode($tag));
        }

        $page = $this->getRequest()->getParam('page');


        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

        $collection->setPageSize((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PERPAGE));
        $collection->setCurPage($page);



        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {
            $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));

            $item->setCreatedTime($this->formatTime($item->getCreatedTime(), Mage::getStoreConfig('blog/blog/dateformat'), true));
            $item->setUpdateTime($this->formatTime($item->getUpdateTime(), Mage::getStoreConfig('blog/blog/dateformat'), true));

            if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_USESHORTCONTENT) && strip_tags(trim($item->getShortContent()))) {
                $content = trim($item->getShortContent());
                $content = $this->closetags($content);
                $content .= ' <a class="read-more" href="' . $this->getUrl($route . "/" . $item->getIdentifier()) . '" >' . $this->__('Read More') . '</a>';
                $item->setPostContent($content);
            } elseif ((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE) != 0) {
                $content = $item->getPostContent();
                if (strlen($content) >= (int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE)) {
                    $content = substr($content, 0, (int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE));
                    $content = substr($content, 0, strrpos($content, ' '));
                    $content = $this->closetags($content);
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
            foreach ($cats as $cat) {
                $catUrls[$cat->getTitle()] = Mage::getUrl($route . "/cat/" . $cat->getIdentifier());
            }
            $item->setCats($catUrls);
        }
        return $collection;
    }

    public function getPages() 
    {
        if ((int) Mage::getStoreConfig('blog/blog/perpage') != 0) {
            $collection = Mage::getModel('blog/blog')->getCollection()
                    ->setOrder('created_time ', 'desc');

            Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

            $currentPage = (int) $this->getRequest()->getParam('page');

            if (!$currentPage) {
                $currentPage = 1;
            }

            $pages = ceil(count($collection) / (int) Mage::getStoreConfig('blog/blog/perpage'));

            $links = "";

            $route = Mage::helper('blog')->getRoute();

            if ($currentPage > 1) {
                $links = $links . '<div class="left"><a href="' . $this->getUrl($route . '/page/' . ($currentPage - 1)) . '" >' . $this->__('Newer Posts') . ' &gt;&gt;</a></div>';
            }
            if ($currentPage < $pages) {
                $links = $links . '<div class="right"><a href="' . $this->getUrl($route . '/page/' . ($currentPage + 1)) . '" >&lt;&lt; ' . $this->__('Older Posts') . '</a></div>';
            }
            echo $links;
        }
    }

    public function closetags($html) 
    {
        #put all opened tags into an array
        preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $result);
        $openedtags = $result[1];

        #put all closed tags into an array
        preg_match_all("#</([a-z]+)>#iU", $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        # all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        # close tags
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= "</" . $openedtags[$i] . ">";
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

}
