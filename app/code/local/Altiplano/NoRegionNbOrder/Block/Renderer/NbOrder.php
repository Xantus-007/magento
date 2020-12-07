<?php

class Altiplano_NoRegionNbOrder_Block_Renderer_NbOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
      $data = $row->getData($this->getColumn()->getIndex());

      if (is_null($data)) {
      	$id = $row->getData('entity_id');

      	$data = (string) Mage::getResourceModel('sales/order_collection')
    			->addFieldToSelect('entity_id')
    			->addFieldToFilter('customer_id', $id)
    			->addFieldToFilter('state', 'complete')
    			->getSize();

				$customer = Mage::getModel('customer/customer')->load($id);
				$customer->setData('nb_order', $data);
				$customer->save();
			}

      return $data;
    }
}

?>