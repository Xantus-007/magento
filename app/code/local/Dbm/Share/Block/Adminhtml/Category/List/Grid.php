<?php

class Dbm_Share_Block_Adminhtml_Category_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('dbmShareCategoryList');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('dbm_share/category')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => 'ID',
            'index' => 'id',
            'width' => 50
        ));

        $this->addColumn('title_fr_fr', array(
            'header' => 'Title FR',
            'index' => 'title_fr_fr'
        ));

        parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $this->setMassactionIdField('id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => 'Supprimer',
            'confirm' => 'Êtes-vous sûr de vouloir supprimer ces catégories?',
            'url' => $this->getUrl('*/*/massaction', array(
                'actioncallback' => 'delete'
            ))
        ));
    }
}