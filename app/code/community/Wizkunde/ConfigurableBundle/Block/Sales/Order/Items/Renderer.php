<?php

class Wizkunde_ConfigurableBundle_Block_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    
    public function getValueHtml($item)
    {
        $result = $this->htmlEscape($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }

        if (!$this->isChildCalculated($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result .= " " . $this->getItem()->getOrder()->formatPrice($attributes['price']);
            }
        }

        $currentItem = ($item->getOrderItem() != null) ? $item->getOrderItem() : $item;

        if($currentItem->getProduct() !== null) {
            $configurableOptions = '';
            if($currentItem->getProduct()->isConfigurable() == true) {
                $configurableOptions = Mage::helper('bundle/catalog_product_configuration')->getConfigurableAttributes($currentItem);
                $extraOptions = Mage::helper('bundle/catalog_product_configuration')->getItemOptions($currentItem);
            }

            $optionValues = Mage::helper('bundle/catalog_product_configuration')->getBundleOrderItemOptions($currentItem);

            return $result . $configurableOptions . $extraOptions . $optionValues;
        }

        return $result;
    }
}