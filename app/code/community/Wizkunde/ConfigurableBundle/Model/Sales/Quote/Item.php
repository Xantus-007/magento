<?php

class Wizkunde_ConfigurableBundle_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function setProduct($product)
    {
        $replaceBySimple = Mage::getStoreConfig('configurablebundle/configurable/replace_by_simple', Mage::app()->getStore()->getId());

        if($replaceBySimple == true && $this->getParentItem() && $product->getTypeId() == 'configurable') {
            $customOptions = $product->getCustomOptions();

            $superAttributes = $this->getBuyRequest()->getSuperAttribute();

            $col = $product->getTypeInstance()
                ->getUsedProductCollection()
                ->addAttributeToSelect('*');

            // Filter for set attributes
            foreach ($superAttributes[$product->getId()] as $attributeKey => $attributeValues) {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeKey);
                $col->addAttributeToFilter($attribute->getName(), array('in', $attributeValues));
            }

            $product = ($col->getFirstItem()) ? $col->getFirstItem() : $product;
            $product->setCustomOptions($customOptions);
        }

        parent::setProduct($product);
    }
}