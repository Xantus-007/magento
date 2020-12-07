<?php
require_once 'Mage/Catalog/controllers/ProductController.php';
class Wizkunde_ConfigurableBundle_AjaxController extends Mage_Catalog_ProductController
{
    protected function getRealProduct($configurableProduct, $attributes)
    {
        if($configurableProduct->getTypeId() == 'configurable') {
            $col = $configurableProduct->getTypeInstance()
                ->getUsedProductCollection()
                ->addAttributeToSelect('*');

            // Filter for set attributes
            foreach ($attributes as $attributeKey => $attributeValues) {
                if ($attributeValues === 0) {
                    return $configurableProduct;
                }

                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeKey);
                $col->addAttributeToFilter($attribute->getName(), array('in', $attributeValues));
            }

            return ($col->getFirstItem()) ? $col->getFirstItem() : $configurableProduct;
        }

        return $configurableProduct;
    }

    public function productgalleryAction()
    {
        $productIds = array();

        $products = json_decode($this->getRequest()->getPost('products'));

        foreach($products as $confProduct => $attributes) {
            $set = true;
            foreach($attributes as $attributeId => $attributeValue) {
                if($attributeValue == 0) {
                    $set = false;
                }
            }

            if($set == true) {
                $product = $this->getRealProduct($confProduct, $attributes);

                $productIds[] = $product->getId();
            } else {
                $productIds[] = $confProduct;
            }
        }

        $collection = Mage::getResourceModel('configurablebundle/image_collection_product');
        $collection->addFieldToFilter('product_id', array('in' => $productIds));

        $images = array();
        foreach($collection as $i => $product) {
            if(isset($images[$product->getImageId()])) {
                $images[$product->getImageId()]++;
            } else {
                $images[$product->getImageId()] = 1;
            }
        }

        foreach($images as $imageId => $count) {
            if($count != count($productIds)) {
                unset($images[$imageId]);
            } else {
                // This is a candidate. Fetch it and see if it has more products attached to it
                $collection = Mage::getResourceModel('configurablebundle/image_collection_product');
                $collection->addFieldToFilter('image_id', array('eq' => $imageId));

                if($collection->count() == $count) {
                    $collection = Mage::getResourceModel('configurablebundle/image_collection_image');
                    $collection->addFieldToFilter('image_id', array('eq' => $imageId));

                    $itemData = array();
                    foreach($collection as $item) {
                        $itemData[] = $item->toArray();
                    }

                    $images = $itemData;
                } else {
                    unset($images[$imageId]);
                }
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($images));
    }
    
    public function productoptionsAction()
    {
        $configurableProduct = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        $product = $this->getRealProduct($configurableProduct, $this->getRequest()->getPost());

        if(!$product instanceof Mage_Catalog_Model_Product) {
            $this->getResponse()->setBody('Cant load product');
        }

        $blockOption = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Options");
        $blockOption->addOptionRenderer("default", "catalog/product_view_options_type_default", "catalog/product/view/options/type/default.phtml");
        $blockOption->addOptionRenderer("text", "catalog/product_view_options_type_text", "catalog/product/view/options/type/text.phtml");
        $blockOption->addOptionRenderer("file", "catalog/product_view_options_type_file", "catalog/product/view/options/type/file.phtml");
        $blockOption->addOptionRenderer("select", "catalog/product_view_options_type_select", "catalog/product/view/options/type/select.phtml");
        $blockOption->addOptionRenderer("date", "catalog/product_view_options_type_date", "catalog/product/view/options/type/date.phtml");


        $blockOptionsHtml = '';

        if($product != null) {
            if($product->getTypeId()=="simple"||$product->getTypeId()=="virtual"||$product->getTypeId()=="configurable")
            {
                if($configurableProduct->getTypeId() == 'configurable') {
                    $blockOption->setProduct($configurableProduct);

                    if(is_array($configurableProduct->getOptions()))
                    {
                        foreach ($configurableProduct->getOptions() as $o)
                        {
                            $blockOptionsHtml .= str_replace('options[', 'bundle_simple_custom_options[' . $configurableProduct->getEntityId() . '][options][', $blockOption->getOptionHtml($o));
                        };
                    } else {
                        $fullProduct = Mage::getModel('catalog/product')->load($product->getId());

                        $blockOption->setProduct($fullProduct);

                        if(is_array($fullProduct->getOptions()))
                        {
                            foreach ($fullProduct->getOptions() as $o)
                            {
                                $blockOptionsHtml .= str_replace('options[', 'bundle_simple_custom_options[' . $fullProduct->getEntityId() . '][options][', $blockOption->getOptionHtml($o));
                            };
                        }
                    }
                }
            }
        }

        $this->getResponse()->setBody($blockOptionsHtml);
    }

    /**
     * Get the required information of a product
     */
    public function productinfoAction()
    {
        $configurableProduct = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        $product = $this->getRealProduct($configurableProduct, $this->getRequest()->getPost());

        if(!$product instanceof Mage_Catalog_Model_Product) {
            $this->getResponse()->setBody('Cant load product');
        }

        $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

        $tierPrice = $product->getPriceModel()->getTierPrice(null, $product);

        $returnData = array(
            'stock' => $stocklevel,
            'name' => $product->getName(),
            'description' => $product->getShortDescription(),
            'price' => (float) $product->getPriceModel()->getFinalPrice(1, $product)
        );

        if(is_array($tierPrice) && count($tierPrice) > 0) {
            foreach($tierPrice as &$price) {
                $price['price'] = (float) $price['price'];
                $price['priceInclTax'] = (float) $price['price'];
                $price['priceExclTax'] = (float) $price['price'];
            }
            
            $returnData['tier_price'] = $tierPrice;
        }

        $this->getResponse()->setBody(Zend_Json::encode($returnData));
    }
}
