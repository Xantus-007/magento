<?php
/**
 * DPD France shipping module for Magento
 *
 * @category   DPDFrance
 * @package    DPDFrance_Shipping
 * @author     Smile, Jibé, DPD France S.A.S. <ensavoirplus.ecommerce@dpd.fr>
 * @copyright  2015 DPD France S.A.S., société par actions simplifiée, au capital de 18.500.000 euros, dont le siège social est situé 27 Rue du Colonel Pierre Avia - 75015 PARIS, immatriculée au registre du commerce et des sociétés de Paris sous le numéro 444 420 830 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class DPDFrance_Export_Block_Export_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('dpdfrexport_export_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare order collection (for different Magento versions)
     * @return DPDFrance_Export_Block_Export_Orders_Grid
     */
    protected function _prepareCollection()
    {
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $collection = Mage::getResourceModel('sales/order_grid_collection')
                ->join('order', "main_table.entity_id = order.entity_id AND order.shipping_method like 'dpdfr%' AND order.status != 'complete' AND order.status != 'canceled' AND order.status != 'holded' AND order.status != 'closed'", array('weight', 'shipping_method'))
                ->join('order_address', "main_table.entity_id = order_address.parent_id AND order_address.address_type = 'shipping'", array('postcode as shipping_postcode', 'city as shipping_city', 'company as shipping_company', 'street as shipping_street', 'country_id as shipping_country_id'));
		} else {
            $collection = Mage::getResourceModel('sales/order_collection')
				->addAttributeToFilter('shipping_method', array('like' => 'dpdfr%'))
				->addAttributeToFilter('status', array('neq' => 'holded'))
				->addAttributeToFilter('status', array('neq' => 'complete'))
				->addAttributeToFilter('status', array('neq' => 'canceled'))
				->addAttributeToFilter('status', array('neq' => 'closed'))
                ->addAttributeToSelect(array('status', 'shipping_method', 'weight'))
				->joinAttribute('shipping_company', 'order_address/company', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_street', 'order_address/street', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_city', 'order_address/city', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_country_id', 'order_address/country_id', 'shipping_address_id', null, 'left')
                
				->addExpressionAttributeToSelect(
                    'shipping_name',
                    'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                    array('shipping_firstname', 'shipping_lastname')
                );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns (for different Magento versions)
     * @return DPDFrance_Export_Block_Export_Orders_Grid
     */
    protected function _prepareColumns(){

	// Javascript remplacer les modes de livraison par des icones
	echo '
	<body onLoad="LoadMyJs()">
	<style media="screen" type="text/css">
		@font-face
		{
			font-family:DPDPlutoSansExtraLight;
			src:url('.Mage::getBaseUrl('media').'dpdfrance/fonts/PlutoSansDPDExtraLight-Web.ttf);
		}

		@font-face
		{
			font-family:DPDPlutoSansLight;
			src:url('.Mage::getBaseUrl('media').'dpdfrance/fonts/PlutoSansDPDLight-Web.ttf);
		}

		@font-face
		{
			font-family:DPDPlutoSansRegular;
			src:url('.Mage::getBaseUrl('media').'dpdfrance/fonts/PlutoSansDPDRegular-Web.ttf);
		}
		#rss_logo p {
			display: inline-block;
			vertical-align: middle;
		}
		#button_hide a {
			margin: 0 auto;
		}
		h3.icon-head.head-export-orders span {
			font-family: DPDPlutoSansRegular !important;
			color: #424143 !important;
		}
	</style>
	<script type="text/javascript">
	function LoadMyJs() {
		var d = document.getElementById("dpdfrexport_export_order_grid_table").children[2]
		if (d)
		{
			for(var i = 0; i < d.childNodes.length; i++)
			{
				if (d.childNodes[i].nodeType == 1)
					if (d.childNodes[i].children[5].innerHTML.search("dpdfrclassic_") > 0 && d.childNodes[i].children[10].innerHTML.search("FR") > 0)
						d.childNodes[i].children[5].innerHTML = "<img title=\"Classic\" alt=\"Classic\" src=\"'.Mage::getBaseUrl('media').'dpdfrance/admin/service_dom.png\"/>";
					else
						if (d.childNodes[i].children[5].innerHTML.search("dpdfrclassic_") > 0 && d.childNodes[i].children[10].innerHTML.search("FR") < 0)
							d.childNodes[i].children[5].innerHTML = "<img title=\"Intercontinental\" alt=\"Intercontinental\" src=\"'.Mage::getBaseUrl('media').'dpdfrance/admin/service_world.png\"/>";
						else
							if (d.childNodes[i].children[5].innerHTML.search("dpdfrrelais_") > 0)
								d.childNodes[i].children[5].innerHTML = "<img title=\"Relais\" alt=\"Relais\" src=\"'.Mage::getBaseUrl('media').'dpdfrance/admin/service_relais.png\"/>";
							else
								if (d.childNodes[i].children[5].innerHTML.search("dpdfrpredict_") > 0)
									d.childNodes[i].children[5].innerHTML = "<img title=\"Predict\" alt=\"Predict\" src=\"'.Mage::getBaseUrl('media').'dpdfrance/admin/service_predict.png\"/>";
			}
		}
	}
	</script>
	';
	
	// Flux RSS
		$rss_location = 'http://www.dpd.fr/extensions/rss/flux_info_dpdfr.xml';
		if (@simplexml_load_file($rss_location))
		{
			$rss = simplexml_load_file($rss_location);
			if (!empty($rss->channel->item))
			{
				echo '
					<script type="text/javascript">
						function show_rss(){
							document.getElementById("div_rss").style.display="block";
							document.getElementById("button_show").style.display="none";
							document.getElementById("button_hide").style.display="block";
						}
						function hide_rss(){
							document.getElementById("div_rss").style.display = "none";
							document.getElementById("button_show").style.display = "block";
							document.getElementById("button_hide").style.display = "none";
						}
						function setup(){
							$(\'deplacer\').update($(\'deplacer\').innerHTML.times(10));
						}
						function MoveNews(){
							new Effect.Move(\'deplacer\', {
								x:-40,
								y:0,
								mode:\'relative\',
								transition:Effect.Transitions.linear,
								afterFinish:MoveNews
							});
						}
						Event.observe(window, "load", function(){
							setup();
							MoveNews();
						});
					</script>
					
					<div id="div_header" style="font-family:DPDPlutoSansLight; width:300px;margin-left: 20px;">
						<div id="rss_logo" style="float:left; width:135px; height:24px; margin-top:-7px;">
							<img src="'.Mage::getBaseUrl('media').'dpdfrance/admin/rss_icon.png" width="24" height="24"/><p> '.Mage::helper('dpdfrexport')->__('DPD News').'</p>
						</div>
						<div id="button_show" style="display:none;">
							<a style="float:left;margin-left:0px;text-decoration: none;color: #424143;height: 24px;width: 24px;font-size: 32px;" href="javascript:void(0)" onclick="show_rss()">+</a>
						</div>
						<div id="button_hide">
							<a style="float:left;margin-left:0px;text-decoration: none;color: #424143;height: 24px;width: 24px;font-size: 32px;" href="javascript:void(0)" onclick="hide_rss()">-</a>
						</div>
					</div>
					<br/>
				';
				echo '
					<div id="div_rss" style="font-family:DPDPlutoSansLight; display:block; background-color: #e6e7e7; color: #424143; border-style: none; margin-left: 20px; margin-right: 20px; margin-top: 10px; padding: 10px;white-space:nowrap;overflow:hidden"><div id="deplacer">';
					foreach ($rss->channel->item as $item)
						echo '<strong style="font-family:DPDPlutoSansRegular; color:#dc0032;">'.$item->category.' > '.$item->title.' : </strong> '.$item->description.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '</div></div></div><br/>';
			}
		}
		// Fin flux RSS
	
        $columnData = array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '70px',
            'type'  => 'text',
            'index' => 'increment_id',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('real_order_id', $columnData);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id', array(
                    'header'    => Mage::helper('sales')->__('Store'),
                    'index'     => 'store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
					'width' => '80px',
                )
            );
        }

        $columnData = array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '120px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('created_at', $columnData);

        $this->addColumn(
            'shipping_name', array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index' => 'shipping_name',
				'width' => '150px',
            )
        );
				
		$columnData = array(
            'header' => 'Service',
            'index' => 'shipping_method',
            'width' => '30px',
			'type'  => 'options',
            'options' => $this->getDPDFranceCarriersOnly()
        );
        $this->addColumn('shipping_method', $columnData);
		
		$columnData = array(
            'header' => Mage::helper('sales')->__('Company').' / '.Mage::helper('dpdfrexport')->__('DPD Pickup point'),
            'index' => 'shipping_company',
            'width' => '140px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('shipping_company', $columnData);
		
		$columnData = array(
            'header' => Mage::helper('sales')->__('Shipping Address'),
            'index' => 'shipping_street',
            'width' => '200px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('shipping_street', $columnData);
		
		$columnData = array(
            'header' => Mage::helper('sales')->__('Code postal'),
            'index' => 'shipping_postcode',
            'width' => '40px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('shipping_postcode', $columnData);
		
		$columnData = array(
            'header' => Mage::helper('sales')->__('City'),
            'index' => 'shipping_city',
            'width' => '160px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('shipping_city', $columnData);		
		
		$columnData = array(
            'header' => Mage::helper('sales')->__('Country'),
            'index' => 'shipping_country_id',
            'width' => '20px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('shipping_country_id', $columnData);
		
        $columnData = array(
            'header'   => Mage::helper('sales')->__('G.T. (Base)'),
            'index'    => 'base_grand_total',
            'type'     => 'currency',
            'currency' => 'base_currency_code'
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('base_grand_total', $columnData);

        $this->addColumn(
            'weight', array(
                'header' => Mage::helper('sales')->__('Weight'),
                'index' => 'weight',
				'type' => 'weight',
				'width' => '40px',
            )
        );

        $columnData = array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'width' => '160px',
            'type'  => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('status', $columnData);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
		
            $this->addColumn(
                'action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                )
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action (for different Magento versions)
     * @return DPDFrance_Export_Block_Export_Orders_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        if (Mage::getVersion() >= '1.4.1') {
            $this->getMassactionBlock()->setUseSelectAll(false);
        }

		// Menu "exporter les commandes - Assurance intégrée"
        $this->getMassactionBlock()->addItem(
            'export_order', array(
                'label'=> Mage::helper('dpdfrexport')->__('Export to DPD Station - Integrated insurance'),
                'url'  => $this->getUrl('*/dpdfrance_export/export'),
            )
        );
				
		// Menu "exporter les commandes - Assurance Ad Valorem"
        $this->getMassactionBlock()->addItem(
            'exportav_order', array(
                'label'=> Mage::helper('dpdfrexport')->__('Export to DPD Station - Ad Valorem insurance'),
                'url'  => $this->getUrl('*/dpdfrance_export/exportav'),
            )
        );

		// Menu "créer les trackings"
		$this->getMassactionBlock()->addItem(
            'tracking_order', array(
                'label'=> Mage::helper('dpdfrexport')->__('Update sent orders'),
                'url'  => $this->getUrl('*/dpdfrance_tracking/tracking'),
            )
        );

        return $this;
    }

    /**
     * Get url called when user click on a grid row 
     * @return string|boolean
     */
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view'))
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        return false;
    }

    /**
     * Get grid url
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true));
    }

	 /**
     * Filtre les transporteurs possibles.
     *
     * @return array
     */
    public function getDPDFranceCarriersOnly()
    {
        $options = array();
		if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
			$collection = Mage::getResourceModel('sales/order_grid_collection')
				->join('order', "main_table.entity_id = order.entity_id AND order.shipping_method like 'dpdfr%' AND order.status != 'complete' AND order.status != 'canceled' AND order.status != 'holded' AND order.status != 'closed'");
		} else{
			$collection = Mage::getResourceModel('sales/order_collection')
				->addAttributeToFilter('shipping_method', array('like' => 'dpdfr%'));
		}
		$this->setCollection($collection);
        foreach ($collection as $option)
            $options[$option->getShippingMethod()] = $option->getShippingMethod();
        return $options;
    }
}
