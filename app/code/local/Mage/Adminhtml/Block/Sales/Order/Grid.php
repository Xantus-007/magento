<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        /*$collection = Mage::getResourceModel($this->_getCollectionClass())
                ->join('sales/order_item','`sales/order_item`.order_id=`main_table`.entity_id',
		array('skus' => new Zend_Db_Expr('group_concat(`sales/order_item`.sku SEPARATOR ",")')));*/
		$collection->getSelect()->group('main_table.entity_id');
		$collection->addFilterToMap('created_at', 'main_table.created_at');
		$collection->addFilterToMap('store_id', 'main_table.store_id');
                
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
                'filter_index' => 'main_table.store_id',
                'filter_condition_callback' => array($this, '_storeFilter')
            ));
        }
        
        //@TODO: reactivate
        $this->addColumn('website_id', array(
            'header'    => Mage::helper('sales')->__('Purchased From (Website)'),
            'type'      => 'options',
            'store_view'=> true,
            'display_deleted' => true,
            'index' => '`core/website`.website_id',
            'filter_condition_callback' => array($this, '_storeFilter'),
            'options' => $this->_getWebsitesAsSelect(),
            'renderer' => new Mage_Adminhtml_Renderer_WebsiteRenderer()
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));
        
        $this->addColumn('surmesure', array(
            'header' => Mage::helper('Sales')->__('Sur Mesure'),
            'width' => '100px',
            'renderer' => new Mage_Adminhtml_Renderer_BentoSurMesure(),
            'type' => 'options',
            'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
            'filter_condition_callback' => array($this, '_surmesureFilter')
        ));
        
        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('Sales')->__('Remise coupon'),
            'width' => '100px',
            'renderer' => new Mage_Adminhtml_Renderer_RemiseCoupon(),
            'type' => 'text',
            'filter_condition_callback' => array($this, '_remiseCouponFilter')
        ));
        
        $this->addColumn('remise_rule', array(
            'header' => Mage::helper('Sales')->__('Remise règle panier'),
            'width' => '100px',
            'renderer' => new Mage_Adminhtml_Renderer_RemiseRule(),
            'type' => 'text',
            'filter_condition_callback' => array($this, '_remiseRuleFilter')
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index' => 'main_table.status'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function _surmesureFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == '') {
            return $this;
        }
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $order_ids = $readConnection->fetchCol('SELECT order_id FROM sales_flat_order_item WHERE sku IN ("bentosurmesure2etages", "1200 02 000", "3000 01 000", "square-perso") GROUP BY order_id');
        switch ($value){
            case 0:
                $collection->addAttributeToFilter('entity_id', array('nin' => array_merge($order_ids)));
                break;
            case 1:
                $collection->addAttributeToFilter('entity_id', array('in' => array_merge($order_ids)));
                break;
        }

        return $this;
    }
    
    protected function _remiseCouponFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == '') {
            return $this;
        }

        $rules = Mage::getModel('salesrule/rule')->getCollection()
                ->addFieldToFilter('name', array('like' => '%' . $value . '%'));
        
        $couponCodeList = array();
        if ($rules) {
            foreach($rules as $rule) {
                $couponCodeList[] = $rule->getCode();
            }
        }
        
        if(count($couponCodeList)) {
            $collection->addAttributeToFilter('sof.coupon_code', array(
                array('in' => $couponCodeList),
                array('like' => '%' . $value . '%')));
        } else {
            $collection->addAttributeToFilter('sof.coupon_code', array('like' => '%' . $value . '%'));
        }
        
        //echo $collection->getSelect();exit;

        return $this;
    }
    
    protected function _remiseRuleFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == '') {
            return $this;
        }

        $rules = Mage::getModel('salesrule/rule')->getCollection()
                ->addFieldToFilter('name', array('eq' => $value))
                ->getAllIds();
        $rules = implode(',', $rules);
        
        $collection->addAttributeToFilter('sof.applied_rule_ids', array('like' => '%'.$rules.'%'));

        return $this;
    }

    protected function _getWebsitesAsSelect()
    {
        $sites = Mage::getModel('core/website')->getCollection();
        $result = array();
        foreach($sites as $site)
        {
            $result[$site->getId()] = $site->getName();
        }

        return $result;
    }

    protected function _storeFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        //$this->getCollection()->addFieldToFilter($value);
        $collection->addFieldToFilter('store_id', $value);
    }

}
