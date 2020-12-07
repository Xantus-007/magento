<?php

class Altiplano_NoRegionNbOrder_Block_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Customer_Edit_Tab_View
{

	  public function fetchView($fileName)
    {
				ob_start();
        include getcwd().'/app/design/adminhtml/default/default/template/customer/tab/view.phtml';
				$html = ob_get_clean();

        $id = parent::getCustomer()->getId();

        $nb = Mage::getResourceModel('sales/order_collection')
    			->addFieldToSelect('entity_id')
    			->addFieldToFilter('customer_id', $id)
    			->addFieldToFilter('state', 'complete')
    			->getSize();

        $html = preg_replace('/<\/table>/', '<tr><td><strong>Nombre de commandes :</strong><td>' . $nb . '</td></tr></table>', $html, 1);

        return $html;
    }

}