<?php

class Monbento_Bundle_Block_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    
    public function getValueHtml($item)
    {
        if ($attributes = $this->getSelectionAttributes($item)) {
            return sprintf('%d', $attributes['qty']) . ' x ' .
                $this->escapeHtml($item->getName());
        } else {
            return $this->escapeHtml($item->getName());
        }
    }

}
