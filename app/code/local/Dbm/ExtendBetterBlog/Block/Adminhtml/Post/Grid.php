<?php

class Dbm_ExtendBetterBlog_Block_Adminhtml_Post_Grid extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Grid
{
    
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
            'entity_id',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'post_title',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Name'),
                'align'     => 'left',
                'index'     => 'post_title',
            )
        );
        
        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'mageplaza_betterblog_post_post_title', 
                array(
                    'header'    => Mage::helper('mageplaza_betterblog')->__('Name in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'mageplaza_betterblog_post_post_title',
                )
            );
        }

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();
            
        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('mageplaza_betterblog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('mageplaza_betterblog')->__('Enabled'),
                    '0' => Mage::helper('mageplaza_betterblog')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'url_key',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('URL key'),
                'index'  => 'url_key',
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
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
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('mageplaza_betterblog')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('mageplaza_betterblog')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('mageplaza_betterblog')->__('XML'));
        return $this;
    }
}
