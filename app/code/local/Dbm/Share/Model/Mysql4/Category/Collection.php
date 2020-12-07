<?php

class Dbm_Share_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_categoryLinkTable;
    protected $_elementTableName;

    public function _construct()
    {
        $resources = Mage::getSingleton('core/resource');
        $this->_init('dbm_share/category', 'dbm_share/category');
        $this->_categoryLinkTable = $resources->getTableName('dbm_share/category_element_relation');
        $this->_elementTableName = $resources->getTableName('dbm_share/element');
    }

    public function toAdminSelectArray()
    {
        $result = array();

        foreach($this as $cat)
        {
            $result[$cat->getId()] = array(
                'label' => $cat->getTitleFrFr(),
                'value' => $cat->getId()
            );
        }

        return $result;
    }

    public function addElementFilter(Dbm_Share_Model_Element $element)
    {
        $this->getSelect()
            ->join(
                $this->_categoryLinkTable,
                'main_table.id = '.$this->_categoryLinkTable.'.id_category'
            )
            ->where($this->_categoryLinkTable . '.id_element = ?', intval($element->getId()));

        return $this;
    }

    public function getIds()
    {
        $result = array();
        foreach($this as $cat)
        {
            $result[] = $cat->getId();
        }

        return $result;
    }

    public function addTypeFilter($type)
    {
        if(Mage::helper('dbm_share')->isTypeAllowed($type))
        {
            $select = $this->getSelect()
                ->distinct()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('id')
                ->columns('main_table.*')
                ->join(
                    array('link' => $this->_categoryLinkTable),
                    'main_table.id = link.id_category',
                    null
                )

                ->joinLeft(
                    array('el' => $this->_elementTableName),
                    'el.id = link.id_element',
                    null
                );

            if($type != Dbm_Share_Model_Element::TYPE_ALL)
            {
                $select->where('type=?', $type);
            }
        }

        return $this;
    }

    public function toApiArray()
    {
        $result = array();
        $helper = Mage::helper('dbm_share');

        foreach($this as $cat)
        {
            $locales = Mage::helper('dbm_share')->getAllowedLocalesWithoutExcludeLocales();
            $tmpResult = array(
                'id' => $cat->getId(),
                'image' => Mage::getBaseUrl('media').$helper->getCategoryImagePath(null, '/').$cat->getImage(),
                'position' => $cat->getPosition()
                    
            );

            foreach($locales as $locale)
            {
                $tmpResult['title_'.$locale] = $cat->getData('title_'.$locale);
                $tmpResult['description_'.$locale] = $cat->getData('description_'.$locale);
            }

            $result[] = $tmpResult;
        }
        
        //Adding simulated popular cat...
        $popularCat = Mage::getModel('dbm_share/category')->load(Dbm_Share_Model_Category::POPULAR_ID);
        if($popularCat->getId())
        {
            $tmpResult = array(
                'id' => Dbm_Share_Model_Category::POPULAR_ID,
                'image' => Mage::getBaseUrl('media').$helper->getCategoryImagePath(null, '/').$popularCat->getImage(),
                'position' => $popularCat->getPosition()
            );
            
            foreach($locales as $locale)
            {
                $tmpResult['title_'.$locale] = $popularCat->getData('title_'.$locale);
                $tmpResult['description_'.$locale] = $popularCat->getData('description_'.$locale);
            }
            
            $result[] = $tmpResult;
        }
        
        usort($result, array($this, '_sortArrayByPosition'));
        
        return $result;
    }
    
    protected function _sortArrayByPosition($a, $b)
    {
        return $a['position'] > $b['position'];
    }
    
    public function sortByPosition()
    {
        $this->getSelect()->order('position');
        
        return $this;
    }
}