<?php
class Wizkunde_ConfigurableBundle_Block_Bundle_Adminhtml_Sales_Order_View_Items_Renderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer
{
    
    public function getValueHtml($item)
    {
        $result = $this->htmlEscape($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }

        if (!$this->isChildCalculated($item) && $this->getItem()->getOrder()) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result .= " " . $this->getItem()->getOrder()->formatPrice($attributes['price']);
            }
        }

        $configurableOptions = '';
        if($item->getProduct()->isConfigurable() == true) {
            $configurableOptions = Mage::helper('bundle/catalog_product_configuration')->getConfigurableAttributes($item);
            $extraOptions = Mage::helper('bundle/catalog_product_configuration')->getItemOptions($item);
        }

        $optionValues = Mage::helper('bundle/catalog_product_configuration')->getBundleOrderItemOptions($item);

        return $result . $configurableOptions . $extraOptions . $optionValues;
    }
}
