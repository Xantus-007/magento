<?php

class Dbm_Share_Block_Adminhtml_Element_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('dbmShareElementList');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection();
        $this->setcollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => 'ID',
            'index' => 'id',
            'width' => 50
        ));

        $this->addColumn('title_fr_fr', array(
            'header' => 'Titre de l\'élément',
            'index' => 'title_fr_fr',
            'width' => 250
        ));

        $this->addColumn('type', array(
            'header' => 'Type',
            'index' => 'type',
            'renderer' => 'Dbm_Share_Block_Adminhtml_Element_List_Renderer_Type'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $this->setMassactionIdField('id');


        $this->getMassActionBlock()->addItem('delete', array(
            'label' => 'Supprimer',
            'confirm' => 'Êtes-vous sûr de vouloir supprimer ces éléments?',
            'url' => $this->getUrl('*/*/massaction', array(
                'actioncallback' => 'delete'
            ))
        ));
    }
}