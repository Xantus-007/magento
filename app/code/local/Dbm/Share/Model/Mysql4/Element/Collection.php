<?php

class Dbm_Share_Model_Mysql4_Element_Collection extends Dbm_Share_Model_Mysql4_Collection_Abstract
{
    protected $_likeTable;
    protected $_commentTable;
    protected $_categoryTable;
    protected $_categoryRelationTable;
    protected $_photoTable;
    protected $_customerLinkTable;

    public function _construct()
    {
        $this->_init('dbm_share/element', 'dbm_share/element');

        $this->_categoryTable = $this->getTable('dbm_share/category');
        $this->_categoryRelationTable = $this->getTable('dbm_share/category_element_relation');
        $this->_likeTable = $this->getTable('dbm_share/like');
        $this->_photoTable = $this->getTable('dbm_share/photo');
        $this->_customerLinkTable = $this->getTable('dbm_customer/link');
    }

    /**
     * Add like count for current element.
     * @return \Dbm_Share_Model_Mysql4_Element_Collection
     */
    public function addLikes()
    {
        $likeCond = new Zend_Db_Expr('(SELECT id_element as like_id_element, COUNT(*) as like_count
            FROM dbm_share_like
            GROUP BY id_element)');

        $select = $this->getSelect();
        $select->joinLeft(array('like_count' => $likeCond), 'main_table.id = like_id_element');

        return $this;
    }

    public function addTypeFilter($type)
    {
        if(Mage::helper('dbm_share')->isTypeAllowed($type) && $type != Dbm_Share_Model_Element::TYPE_ALL)
        {
            /*
            $select = $this->getSelect();
            $select->where('type=?', $type);
             */
            $this->addFieldToFilter('type', $type);
        }

        return $this;
    }

    public function addAll()
    {
        return $this->addLikes();
    }

    public function toApiArray()
    {
        $result = array();

        foreach($this as $element)
        {
            $result[] = $element->toApiArray();
        }

        return $result;
    }

    public function addCustomerFilter(Mage_Customer_Model_Customer $customer)
    {
        $select = $this->getSelect()
            ->where('`main_table`.id_customer=?', $customer->getId());

        return $this;
    }

    public function addFollowedByFilter(Mage_Customer_Model_Customer $customer)
    {
        $select = $this->getSelect();
        $select->join(array('customer_link' => $this->_customerLinkTable), 
            'main_table.id_customer = customer_link.id_following', 
            ''
        );

        $select->where('customer_link.id_customer = ?', $customer->getId());

        return $this;
    }

    public function addCategoryFilter(Dbm_Share_Model_Category $category)
    {
        if($category->getId() > 0)
        {
            $select = $this->getSelect()
                ->join(array('category_link' => $this->_categoryRelationTable), 'main_table.id = category_link.id_element')
                ->where('category_link.id_category = ?', $category->getId())
            ;
        }

        return $this;
    }

    public function orderByLikes()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER)
            ->order('like_count DESC');
        return $this;
    }

    public function orderByDate()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER)
            ->order('main_table.created_at DESC');
        return $this;
    }
    
    public function orderByLikedDate(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getId())
        {
            $this->getSelect()->reset(Zend_Db_Select::ORDER)
                //->join(array('liked_by_customer' => $this->_likeTable), 'main_table.id = liked_by.id_element  liked_by.id_customer = ?', $customer->getId())
                ->columns('i_like.created_at AS liked_on')
                ->order('liked_on DESC')
            ;
        }
        
        return $this;
    }

    public function addLikedByFilter(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getId())
        {
            $select = $this->getSelect();
            $select->join(array('liked_by' => $this->_likeTable),
                'main_table.id = liked_by.id_element',
                ''
            );
            $select->where('liked_by.id_customer = ?', $customer->getId());
        }

        return $this;
    }

    public function addCustomerLikes($customer)
    {
        if($customer->getId())
        {
            $select = $this->getSelect();

            $select->joinLeft(array('i_like'=> $this->_likeTable),
                    'main_table.id = i_like.id_element AND i_like.id_customer = '.intval($customer->getId()),
                    'i_like.id_customer as liked_by'
                )
            ;
        }

        return $this;
    }
    
    public function getLikeSum()
    {
        $select = $this->getSelect();
        $count = new Zend_Db_Expr('SUM(like_count) as sum_likes');
        $select->from('', $count);
        
        return $this;
    }

    public function search($searchString, $type = null)
    {
        $select = $this->getSelect();
        $helper = Mage::helper('dbm_share');
        $locales = Mage::helper('dbm_share')->getAllowedLocalesWithoutExcludeLocales();
        $clauses = array();
        $rAdapter = $this->getConnection();

        foreach($locales as $locale)
        {
            $clauses[] = $rAdapter->quoteInto('title_'.$locale.' LIKE ?', '%'.str_replace(' ', '%', $searchString).'%'); 
        }

        if(count($clauses))
        {
            $select->where(implode(' OR ', $clauses));
        }

        $this->addTypeFilter($type);

        return $this;
    }
    
    public function addBoundsFilter(Dbm_Map_Model_Bounds $bounds)
    {
        $select = $this->getSelect();
        $select->joinLeft(array('photo_bounds' => $this->_photoTable), 
            'photo_bounds.id_element = main_table.id',
            array('photo_bounds.lat', 'photo_bounds.lng', 'photo_bounds.filename')
        );
        
        $this->addFieldToFilter('photo_bounds.lat', array(
            'gt' => $bounds->getSouthWest()->lat
        ));
        $this->addFieldToFilter('photo_bounds.lat', array(
            'lt' => $bounds->getNorthEast()->lat
        ));
        
        $this->addFieldToFilter('photo_bounds.lng', array(
            'gt' => $bounds->getSouthWest()->lng
        ));
        $this->addFieldToFilter('photo_bounds.lng', array(
            'lt' => $bounds->getNorthEast()->lng
        ));
        
        $this->addFieldToFilter('photo_bounds.lat', array(
            'notnull' => true
        ));
        $this->addFieldToFilter('photo_bounds.lng', array(
            'notnull' => true
        ));
        
        return $this;
    }

    public function addLocaleFilter($locales)
    {
        $select =  $this->getSelect();
        $tmpOrWhere = array();
        
        foreach($locales as $locale => $active)
        {
            $locale = strtolower($locale);
            
            if(Mage::helper('dbm_share')->isLocaleAllowed($locale))
            {
                //$type = $active == 1 ? 'notnull' : 'eq';
                if($active == 1)
                {
                    $tmpOrWhere[] = new Zend_Db_Expr('(title_'.$locale.' IS NOT NULL AND title_'.$locale.' != \'\')');      
                    //$this->addFieldToFilter('title_'.$locale, array('notnull' => true));
                    //$this->addFieldToFilter('title_'.$locale, array('neq' => ''));
                }
            }
        }
        
        $where = implode(' OR ', $tmpOrWhere);
        
        if(strlen($where))
        {
            $select->where('('.$where.')');
        }
        
        return $this;
    }
}
