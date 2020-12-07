<?php

class Dbm_Share_Block_Adminhtml_Comments_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('dbmShareCommentsList');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
//        $collection = Mage::getModel('dbm_share/comment')->getCollection();
        $collection = Mage::getModel('dbm_share/comment')
                        ->getCollection()
                        ->addExpressionFieldToSelect('count', 'COUNT(dbm_share_abuse_comment.id_comment)' , 'dbm_share_abuse_comment.id')
                ;
        $collection ->getSelect()
                    ->joinLeft('dbm_share_abuse_comment','main_table.id=dbm_share_abuse_comment.id_comment')
                    ->group('main_table.id')
                            ;
        
        
//        echo $collection->getSelect();exit();
        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => 'ID',
            'index'  => 'id',
            'width'  => 50
        ));

        $this->addColumn('comment', array(
            'header' => 'Commentaire',
            'index'  => 'message'
        ));
        
        $this->addColumn('status', array(
            'header'  => 'En Ligne',
            'index'   => 'status',
            'type'    => 'options',
            'options' => array('1' => 'Oui', '0' => 'Non')
        ));
        
        $this->addColumn('report', array(
            'header' => 'Report',
            'index'  => 'count'
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
            'confirm' => 'Êtes-vous sûr de vouloir supprimer ces commentaires?',
            'url' => $this->getUrl('*/*/massaction', array(
                'actioncallback' => 'delete'
            ))
        ));
    }
}