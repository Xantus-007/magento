<?php
class Monbento_Bundle_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
	/*
	1-Couvercle Supérieur
	2-Couvercle intermédiare haut (3 étages)
	3-Couvercle intermédiare milieu (3 étages)
	4-Couvercle intermédiare bas (3 étages)
	5-Couvercle intermédiare haut (2 étages)
	6-Couvercle intermédiare bas (2 étages)
	7-Récipient haut (3 étages)
	8-Récipient milieu (3 étages)
	9-Récipient bas (3 étages)
	10-Récipient haut (2 étages)
	11-Récipient bas (2 étages)
	12-Elastique
	*/
    public function getCustomImageType($option)
    { 		 
			 switch ($option) {
				 case 1:
				 case 2:
				 case 5:
				 case 7:
				 case 10:
				 case 12:
				 $_imageType = 'image_bundle_1';
				 break;
				 case 3:
				 case 8:
				 $_imageType = 'image_bundle_2';
				 break;
				 case 4:
				 case 6:
				 case 9:
				 case 11:
				 $_imageType = 'image_bundle_3';
				 break;
				 default:
				 $_imageType = 'image_bundle_1';
				 break;
             }
			 return $_imageType;
    }
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('current_product'));
        }
        return $this->getData('product');
    }

    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        return $_selection->getSelectionQty()*1 . ' x ' . $_selection->getName() . ' &nbsp; ' .
            ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }

    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        return $_selection->getName() . ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }

    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' . $elementId . '\').advaiceContainer = \'' . $containerId . '\';
            $(\'' . $elementId . '\').callbackFunction  = \'bundle.validationCallback\';
            </script>';
    }

    public function formatPriceString($price, $includeContainer = true)
    {
        $priceTax = Mage::helper('tax')->getPrice($this->getProduct(), $price);
        $priceIncTax = Mage::helper('tax')->getPrice($this->getProduct(), $price, true);

        if (Mage::helper('tax')->displayBothPrices() && $priceTax != $priceIncTax) {
            $formated = Mage::helper('core')->currency($priceTax, true, $includeContainer);
            $formated .= ' (+'.Mage::helper('core')->currency($priceIncTax, true, $includeContainer).' '.Mage::helper('tax')->__('Incl. Tax').')';
        } else {
            $formated = $this->helper('core')->currency($priceTax, true, $includeContainer);
        }

        return $formated;
    }
}
