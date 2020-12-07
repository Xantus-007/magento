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
 
class DPDFrance_Export_Block_Tracking_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'dpdfrexport';
        $this->_controller = 'tracking_orders';
        $this->_headerText = Mage::helper('dpdfrexport')->__('Suivi des colis DPD France');
        parent::__construct();
        $this->_removeButton('add');
    }
}