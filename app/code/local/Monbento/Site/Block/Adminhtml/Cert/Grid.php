<?php

class Monbento_Site_Block_Adminhtml_Cert_Grid extends Unirgy_Giftcert_Block_Adminhtml_Cert_Grid 
{

    protected function _prepareColumns() 
    {
        parent::_prepareColumns();
        $hlp = Mage::helper('ugiftcert');

        $this->addColumn('cert_id', array(
            'header' => $hlp->__('Certificate ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'cert_id',
            'header_export' => 'cert_id',
            'filter_index' => 'main_table.cert_id',
            'type' => 'number',
        ));

        $this->addColumn('cert_number', array(
            'header' => $hlp->__('Certificate Code'),
            'align' => 'left',
            'index' => 'cert_number',
            'header_export' => 'cert_number',
        ));

        $this->addColumn('amount', array(
            'header' => $hlp->__('Initial amount'),
            'align' => 'right',
            'index' => 'amount',
            'header_export' => 'amount',
            'type' => 'currency',
            'currency' => 'currency_code',
        ));

        $this->addColumn('balance', array(
            'header' => $hlp->__('Balance'),
            'align' => 'right',
            'header_export' => 'balance',
            'index' => 'balance',
            'type' => 'currency',
            'currency' => 'currency_code',
        ));

        $this->addColumn('status', array(
            'header' => $hlp->__('Status'),
            'index' => 'status',
            'header_export' => 'status',
            'type' => 'options',
            'filter_index' => 'main_table.status',
            'options' => array(
                'P' => $hlp->__('Pending'),
                'A' => $hlp->__('Active'),
                'I' => $hlp->__('Inactive'),
            ),
        ));
        $this->addColumn('valide', array(
            'header' => $hlp->__('Carte valide'),
            'renderer' => new Mage_Adminhtml_Renderer_Enabledgift(),
            'header_export' => 'valide',
            'type' => 'options',
            'filter_condition_callback' => array($this, '_enabledFilter'),
            'options' => array(0 => $this->__('Non'), 1 => $this->__('Oui'))
        ));


        $this->addColumn('customer_email', array(
            'header' => $hlp->__('Customer Created'),
            'align' => 'left',
            'index' => 'customer_email',
            'header_export' => 'customer_email',
        ));

        $this->addColumn('order_increment_id', array(
            'header' => $hlp->__('Order ID'),
            'align' => 'left',
            'index' => 'order_increment_id',
            'header_export' => 'order_increment_id',
        ));

        $this->addColumn('recipient_name', array(
            'header' => $hlp->__('Recipient Name'),
            'align' => 'left',
            'index' => 'recipient_name',
            'header_export' => 'recipient_name',
        ));
        $this->addColumn('recipient_email', array(
            'header' => $hlp->__('Recipient Email'),
            'align' => 'left',
            'index' => 'recipient_email',
            'header_export' => 'recipient_email',
        ));

        $this->addColumn('ts', array(
            'header' => $hlp->__('Created At'),
            'align' => 'left',
            'index' => 'ts',
            'header_export' => 'ts',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('expire_at', array(
            'header' => $hlp->__('Expires On'),
            'align' => 'left',
            'index' => 'expire_at',
            'header_export' => 'expire_at',
            'type' => 'date',
            'width' => '120px',
        ));

        $this->addColumn('store_id', array(
            'header' => $this->__('Store View'),
            'width' => '200px',
            'index' => 'store_id',
            'header_export' => 'store_id',
            'type' => 'store',
            'store_all' => false,
            'store_view' => true,
        ));

        $this->addColumn('username', array(
            'header' => $hlp->__('Admin Created'),
            'align' => 'left',
            'index' => 'username',
            'header_export' => 'username',
        ));
        if ($this->_isExport) {
            $allowedCols = Unirgy_Giftcert_Helper_Import::getImportFields();
            foreach ($this->getColumns() as $id => $col) {
                if (in_array($id, $allowedCols)) {
                    unset($allowedCols[array_search($id, $allowedCols)]);
                }
            }
            foreach ($allowedCols as $colId) {
                $this->addColumn($colId, array(
                    'header' => $colId,
                    'header_export' => $colId,
                    'index' => $colId,
                ));
            }
        }
        $this->addExportType('*/*/exportCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('adminhtml')->__('XML'));

        return $this;
    }
    
    protected function _enabledFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        
        if ($value == '') {
            return $this;
        }
        
        $lstOrderCertIds = Mage::getModel('ugiftcert/history')->getCollection()
                ->addFieldToFilter('action_code', array('eq' => 'order'))
                ->addFieldToFilter('status', array('eq' => 'A'))
        ;
                
        $ids = array();
        foreach($lstOrderCertIds as $historyCert)
        {
            $ids[] = $historyCert->getCertId();
        }
        
        switch($value)
        {
            case 0:
                $collection->addFieldToFilter('main_table.cert_id', array('nin' => $ids));
                break;
            case 1:
                $collection->addFieldToFilter('main_table.cert_id', array('in' => $ids));
                break;
        }

        return $this;
    }

}
