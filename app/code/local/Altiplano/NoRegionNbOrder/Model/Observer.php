<?php

class Altiplano_NoRegionNbOrder_Model_Observer
{

	public function eventSave($observer)
	{
		$id = (int) $observer->getEvent()->getOrder()->getCustomerId();
		if ($id) {
			$data = (string) Mage::getResourceModel('sales/order_collection')
    		->addFieldToSelect('entity_id')
    		->addFieldToFilter('customer_id', $id)
    		->addFieldToFilter('state', 'complete')
    		->getSize();

			$customer = Mage::getModel('customer/customer')->load($id);
			$customer->setData('nb_order', $data);
			$customer->save();
		}
	}

}