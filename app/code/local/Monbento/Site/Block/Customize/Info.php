<?php

class Monbento_Site_Block_Customize_Info extends Mage_Core_Block_Template 
{
    public function getBundlePrice()
    {
        $bundled_product = Mage::registry('product');
        $_priceModel  = $bundled_product->getPriceModel();

        list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getTotalPrices($bundled_product, null, null, false);

        return $_minimalPriceTax;
    }

    public function getOptionsPrice()
    {
        $bundled_product = Mage::registry('product');
        $params = Mage::app()->getRequest()->getParams();

        $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection($bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product);

        $optionsSelected = array();

        $matiereId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'matiere');
        $colorId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'color');

        foreach($selectionCollection as $option)
        {
            $configurableProduct = Mage::getModel('catalog/product')->load($option->product_id);
            $configurableProductItems = $configurableProduct->getTypeInstance(true)->getUsedProducts(null, $configurableProduct);

            $basePrice = $configurableProduct->getFinalPrice();
            foreach($configurableProductItems as $product)
            {
                if($params['bundle-option-'.$option->option_id] == $product->getId())
                {
                    $priceOption = max(0, ($product->getFinalPrice() - $basePrice));
                    $optionsSelected[$option->option_id] = array(
                        "selection_id" => $option->selection_id,
                        "product_id" => $product->getId(),
                        "name" => $product->getName(),
                        "priceOption" => $priceOption,
                        "superAttributes" => array(
                            $matiereId => $product->getMatiere(),
                            $colorId => $product->getColor()
                        )
                    );
                }
            }
        }

        return $optionsSelected;
    }

}