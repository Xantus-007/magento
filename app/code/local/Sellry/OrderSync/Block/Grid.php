<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.2
 */

class Sellry_OrderSync_Block_Grid extends Mage_Adminhtml_Block_Abstract {
    protected function  _prepareLayout() {
        $this->setTemplate('ordersync/grid.phtml');
        return parent::_prepareLayout();
    }
}