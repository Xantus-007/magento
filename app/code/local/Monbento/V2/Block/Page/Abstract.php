<?php

class Monbento_V2_Block_Page_Abstract extends Mage_Core_Block_Template
{
    public function getPage()
    {
        $currentPage = Mage::getBlockSingleton('cms/page')->getPage();
        
        return $currentPage;
        
        //print_r($currentPage->getData());
        //exit();
    }
    
    public function getDb()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
    
    public function getParentPages(Mage_Cms_Model_Page $page)
    {
        $db = $this->getDb();
        $handles = $this->getLayout()->getUpdate()->getHandles();
        
        $pageRecrutement = Mage::getModel('cms/page')->load('monbento-recrute', 'identifier');

        $pageId = (in_array('recrutement', $handles)) ? $pageRecrutement->getParent() : $page->getId();
        return $db->fetchAll("SELECT identifier, libelle, page_id FROM cms_page WHERE parent = ? AND is_active = 1 ORDER BY position, page_id", $pageId);
    }
    
    public function getCollection(Mage_Cms_Model_Page $page)
    {
        $db = $this->getDb();
        $result = $this->getParentPages($page);
        
        if(!count($result))
        {
            $result = $db->fetchAll('SELECT identifier, libelle, page_id FROM cms_page WHERE parent = ? AND is_active = 1 ORDER BY position, page_id', $page->getParent());
        }
        
        return $result;
    }
    
    public function getMenu()
    {   
        $page = $this->getPage();
        $handles = $this->getLayout()->getUpdate()->getHandles();
        
        $pageRecrutement = Mage::getModel('cms/page')->load('monbento-recrute', 'identifier');
        
        $result = array();
        if ($page)
        {
            if (empty($res))
            { // pages de même niveau
                $res = $this->getCollection($page);
                if(in_array('recrutement', $handles)) $page = $pageRecrutement;
                
                if (is_array($res))
                {
                    foreach ($res as $v)
                    {
                        
                        $result[] = array(
                            'isActive' => $v['page_id'] == $page->getId(),
                            'url' => $this->getUrl($v['identifier']),
                            'label' => htmlentities($v['libelle'], ENT_NOQUOTES, 'UTF-8')
                        );
                    }
                }
            }
        }

        return $result;
    }

    public function getBreadcrumbs()
    {
        $page = $this->getPage();
        $result = array();
        $hasParent = count($this->getParentPages($page)) == 0;
        
        if ($page)
        {
            $result[] = array(
                'url' => Mage::getBaseUrl('web'),
                'label' =>  Mage::helper('cms')->__('Home')
            );
            
            if ($hasParent)
            {
                $collection = $this->getCollection($page);
                $res = reset($collection);
                if (count($res))
                {
                    $label = $res['libelle'] ? $res['libelle'] : ($res['content_heading'] ? $res['content_heading'] : $res['title']);
                    $result[] = array(
                        'url' => Mage::getBaseUrl('web') . $res['identifier'],
                        'label' => $label
                    );
                }
            }
            
            $lastLabel = $page->getLibelle() ? $page->getLibelle() : ($page->getContentHeading() ? $page->getContentHeading() : $page->getTitle());
            $result[] = array(
                'url' => '',
                'label' => $lastLabel
            );
            
        }
        
        return $result;
    }
}
