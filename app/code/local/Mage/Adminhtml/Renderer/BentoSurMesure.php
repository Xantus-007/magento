<?php 

class Mage_Adminhtml_Renderer_BentoSurMesure extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter("order_id", $row->getEntityId());
    	foreach ($items as $item)
    	{
    		$skus = array($item->getSku());
    		$productOptions = $item->getProductOptions();
    		if($item->getProductType() == 'bundle')
    		{
    			if($productOptions['product_calculations'] == 0) return 'Oui';
    		}
    		if(in_array("bentosurmesure2etages", $skus) || in_array("1200 02 000", $skus) || in_array("3000 01 000", $skus) || in_array("square-perso", $skus)) return 'Oui';
		}

		/*$skus = explode(",",$row->getSkus());	
		if (in_array("bentosurmesure2etages", $skus) || in_array("1200 02 000", $skus) || in_array("3000 01 000", $skus) || in_array("square-perso", $skus)) {
			return 'Oui';
		} else {
			return 'Non';
		}*/
		return 'Non';
    }
}
