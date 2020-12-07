<?php

class Monbento_Site_Block_Adminhtml_Posts_Grid extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Grid
{
    
    /**
     * prepare collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Grid
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageplaza_betterblog/post')
            ->getCollection()
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('url_key');
        
        if($catsId = $this->getRequest()->getParam('category')) 
        {
            $catsId = explode('-', $catsId);

            $collection->getSelect()->joinLeft(array('cat' => 'mageplaza_betterblog_post_category'), 'e.entity_id = cat.post_id');
            $collection->getSelect()->where('cat.category_id IN (?)', $catsId)->group('e.entity_id');
        }
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
        $collection->joinAttribute(
            'post_title', 
            'mageplaza_betterblog_post/post_title', 
            'entity_id', 
            null, 
            'inner', 
            $adminStore
        );
        if ($store->getId()) {
            $collection->joinAttribute(
                'mageplaza_betterblog_post_post_title', 
                'mageplaza_betterblog_post/post_title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
        }

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    /**
     * prepare grid collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Grid
     * @author Sam
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('mageplaza_betterblog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('mageplaza_betterblog')->__('Edit'),
                        'url'     => array('base'=> '*/betterblog_post/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
    }

    /**
     * get the row url
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post
     * @return string
     * @author Sam
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/betterblog_post/edit', array('id' => $row->getId()));
    }
}
