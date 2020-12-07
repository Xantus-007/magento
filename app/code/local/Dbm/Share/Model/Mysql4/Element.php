<?php

class Dbm_Share_Model_Mysql4_Element extends Dbm_Share_Model_Mysql4_Abuse_Abstract
{
    protected $_categoryLinkTable;
    protected $_likeTableName;
    protected $_write;

    public function _construct()
    {
        $this->_init('dbm_share/element', 'id');
        $this->_categoryLinkTable = Mage::getSingleton('core/resource')->getTableName('dbm_share/category_element_relation');
        $this->_likeTableName = Mage::getSingleton('core/resource')->getTableName('dbm_share/like');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    public function saveCategoryIds($elementId, $catIds)
    {
        if($elementId > 0)
        {
            $sql = 'DELETE FROM '.$this->_categoryLinkTable.' WHERE id_element=?';
            $this->_write->query($sql, array($elementId));

            if(is_array($catIds))
            {
                foreach($catIds as $catId)
                {
                    $sql = 'INSERT INTO '.$this->_categoryLinkTable.'(id_element, id_category) VALUES(?, ?)';
                    $this->_write->query($sql, array($elementId, $catId));
                }
            }
        }

        return $this;
    }

    public function toApiArray(Dbm_Share_Model_Element $element)
    {
        $trans = Mage::helper('dbm_share');
        $result = $element->toArray();
        $tmpPhotos = $element->getPhotos()->toApiArray();
        $result['photos'] = array();
        
        if(count($tmpPhotos))
        {
            $result['photos'] = $tmpPhotos;
        }
        
        //Has liked by current customer
        $currentCustomer = Mage::helper('dbm_customer')->getCurrentCustomer();      
        if($currentCustomer && $currentCustomer->getId())
        {
            $result['i_liked'] = $element->isLikedBy($currentCustomer);
        }
        else
        {
            $result['i_liked'] = false;
        }
        
        $result['level'] = intval($result['level']);
        $result['price'] = intval($result['price']);
        
        $locales = $trans->getAllowedLocalesWithoutExcludeLocales();
        
        foreach($locales as $locale)
        {
            if(strlen($result['ingredients_legend_'.$locale]))
            {
                $key = 'ingredients_legend_'.$locale;
                /*
                $val = intval($result[$key]);
                $res = '';
                
                if($val > 1)
                {
                    $res = $trans->__('%d persons', intval($result[$key]));
                }
                elseif($result[$key] == 1)
                {
                    $res = $trans->__('%d person', intval($result[$key]));
                }
                else
                {
                    $res = '';
                }
                 */
                
                $result[$key] = intval($result[$key]);
            }
        }

        $unsetData = array(
            'id_element', 'like_at'
        );
        $author = Mage::getModel('dbm_customer/customer')->load($element->getIdCustomer());
        $result['author'] = Mage::getModel('dbm_customer/customer')->toApiArray($author);
        $result['comment_count'] = $element->getComments()->count();
        
        foreach($unsetData as $attribute)
        {
            unset($result[$attribute]);
        }
        
        $result['link'] = $element->getLink();
        
        return $result;
    }

    public function toggleLike(Mage_Customer_Model_Customer $customer, Dbm_Share_Model_Element $element, $like = true)
    {
        $like = $like === true ? true : false;
        $rAdapter = $this->_getReadAdapter();
        $wAdapter = $this->_getWriteAdapter();

        if($element->getId() > 0 && $customer->getId() > 0)
        {
            $select = $rAdapter->select();
            $select->from($this->_likeTableName)
                ->where('id_element = ?', $element->getId())
                ->where('id_customer = ?', $customer->getId())
            ;

            $hasLike = $rAdapter->fetchOne($select);

            if($hasLike && !$like)
            {
                $where = $wAdapter->quoteInto('id_element = ?', $hasLike)
                    .' AND '.$wAdapter->quoteInto(' id_customer = ?', $customer->getId());
                $wAdapter->delete($this->_likeTableName, $where);
            }
            elseif(!$hasLike && $like)
            {
                $now = Mage::app()->getLocale()->date()->toString('yyyy-MM-dd HH:mm:ss');

                $this->_getWriteAdapter()->insert($this->_likeTableName, array(
                    'id_element' => $element->getId(),
                    'id_customer' => $customer->getId(),
                    'created_at' => $now
                ));
            }
        }

        return $this;
    }
}