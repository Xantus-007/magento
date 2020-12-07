<?php

class Monbento_Site_Block_Customize_Configurables extends Wizkunde_ConfigurableBundle_Block_Catalog_Product_View_Type_Bundle
{
    protected $_bundleProduct;

    public function getBundleItems()
    {
        $bundled_product = $this->_initProduct();
        $bundled_items = array();

        $imageProductsCheck = array();
        $selectionCollectionReorder = array();
        foreach($bundled_product->getTypeInstance(true)->getOptions($bundled_product) as $optionPosition)
        {
            $optionId = $optionPosition->getOptionId();
            $selectionCollectionReorder[$optionId] = $optionPosition->getPosition();
        }

        $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection($bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product);
        foreach($selectionCollection as $option)
        {
            $configurableProduct = Mage::getModel('catalog/product')->load($option->getProductId());
            $_galleryConfigurable = $configurableProduct->getMediaGallery('images');

            $galleryChoice = (in_array($option->getProductId(), $imageProductsCheck)) ? 'gallery5' : 'gallery3';
            if($bundled_product->getIsSingle()) $galleryChoice = 'gallery4';
            foreach ($_galleryConfigurable as $_image)
            {
                if($_image[$galleryChoice] == '1')
                {
                    $image = $_image['file'];
                }

                if(strpos($bundled_product->getSku(), 'square') !== false && $_image['gallery6'] == '1')
                {
                    $image = $_image['file'];
                    break;
                }
            }

            $optionId = $option->getOptionId();
            $bundled_items[$selectionCollectionReorder[$optionId]] = array(
                'optionId' => $option->getOptionId(),
                'selectionId' => $option->getSelectionId(),
                'configurableId' => $option->getProductId(),
                'image' => Mage::helper('dbm_utils/image')->resizeImage('media/catalog/product' . $image, 100, 80)
            );
            ksort($bundled_items);

            $imageProductsCheck[] = $option->getProductId();
        }

        return $bundled_items;
    }

    public function getBundleOptions($isRandom = false, $reset = false)
    {
        $bundled_product = $this->_initProduct();
        $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection($bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product);
        $bundled_items = array();

        $bundleExplodedView = 0;
        $_galleryBundle = $bundled_product->getMediaGallery('images');
        $galleryChoice = ($bundled_product->getIsSingle()) ? 'gallery4' : 'gallery3';
        foreach ($_galleryBundle as $_imageBundle)
        {
            if($_imageBundle[$galleryChoice] == '1')
            {
                $bundleExplodedView++;
            }
        }

        $imageProductsCheck = array();

        foreach($selectionCollection as $option)
        {
            $configurableProduct = Mage::getModel('catalog/product')->load($option->product_id);
            $_galleryConfigurable = $configurableProduct->getMediaGallery('images');
            $productForExplodedView = false;
            foreach ($_galleryConfigurable as $_imageConf)
            {
                if(strpos($bundled_product->getSku(), 'square') !== false && !$bundled_product->getIsSingle() && $_imageConf['gallery6'] == '1')
                {
                    $productForExplodedView = true;
                }
            }
            $attributesConfigurable = $this->getAllowAttributes($configurableProduct);

            $configurableItems = array();

            foreach ($attributesConfigurable as $attribute)
            {
                $productAttribute = $attribute->getProductAttribute();
                if($productAttribute->getAttributeCode() == 'matiere')
                {
                    $options = $productAttribute->getSource()->getAllOptions(false);
                    foreach ($options as $optionAttribute)
                    {
                        $configurableItems[$optionAttribute['value']] = array('label' => $optionAttribute['label'], 'products' => array());
                    }
                }
            }

            $productsCount = 0;
            $configurableProductItems = $configurableProduct->getTypeInstance(true)->getUsedProducts(null, $configurableProduct);

            $itemsIds = array();
            foreach($configurableProductItems as $product)
            {
                $product = Mage::getModel('catalog/product')->load($product->getId());
                $itemId = $product->getId();

                if($product->isSaleable())
                {
                    if($matiereId = $product->getMatiere()) $itemsIds[$itemId] = $product;
                }
            }

            if($isRandom) $randomIndex = rand(0, (count($itemsIds) - 1));

            foreach($itemsIds as $productId => $product)
            {
                $basePrice = $configurableProduct->getFinalPrice();
                $matiereId = $product->getMatiere();

                $images = $this->_getImagesArrayForSelectionItem($bundled_product, $configurableProduct, $product, $productForExplodedView, $bundleExplodedView, $imageProductsCheck);

                if($isRandom)
                {
                    $isActive = $productsCount == $randomIndex;
                }
                elseif(Mage::app()->getRequest()->getParam('bundle-option-'.$option->option_id) && !$reset)
                {
                    $isActive = Mage::app()->getRequest()->getParam('bundle-option-'.$option->option_id) == $product->getId();
                }
                else
                {
                    $isActive = $product->getIsFeatured() == 1;
                }

                $priceOption = max(0, ($product->getFinalPrice() - $basePrice));

                if($product->getImageBundleMotif() && $product->getImageBundleMotif() != 'no_selection')
                {
                    $imageMotif = Mage::helper('dbm_utils/image')->resizeImage('media/catalog/product' . $product->getImageBundleMotif(), 35, 35);
                }
                else
                {
                    $imageMotif = '#'.Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'color_hexa')->getFrontend()->getValue($product);
                }

                $configurableItems[$matiereId]['products'][] = array(
                    'productId' => $product->getId(),
                    'color' => $product->getAttributeText('color'),
                    'priceOption' => $priceOption,
                    'imageMotif' => $imageMotif,
                    'images' => $images,
                    'isActive' => $isActive
                );

                $productsCount++;
            }

            $bundled_items[$option->option_id] = array(
                'selectionId' => $option->selection_id,
                'configurableOptions' => $configurableItems,
            );

            $imageProductsCheck[] = $option->product_id;
        }

        return $bundled_items;
    }

    public function getBasePrice()
    {
        $bundled_product = $this->_initProduct();
        $_priceModel  = $bundled_product->getPriceModel();

        list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getTotalPrices($bundled_product, null, null, false);

        return $_minimalPriceTax;
    }

    public function getNbViews()
    {
        $bundled_product = $this->_initProduct();
        $galleryChoice = ($bundled_product->getIsSingle()) ? 'gallery4' : 'gallery3';
        $_gallery = $bundled_product->getMediaGallery('images');
        $nb = 0;

        foreach ($_gallery as $_image)
        {
            if($_image[$galleryChoice] == '1')
            {
                $nb++;
            }
        }

        return $nb;
    }

    protected function _initProduct()
    {
        if(is_null($this->_bundleProduct))
        {
            $bundled_product = new Mage_Catalog_Model_Product();
            $bundled_product->load($this->getProduct()->getId());

            $this->_bundleProduct = $bundled_product;
        }

        return $this->_bundleProduct;
    }

    protected function _getImagesArrayForSelectionItem($bundled_product, $configurableProduct, $product, $productForExplodedView, $bundleExplodedView, $imageProductsCheck)
    {
        $images = array();
        $_gallery = $product->getMediaGallery('images');
        $_galleryReorder = array();
        foreach ($_gallery as $image)
        {
            $positionDefault = ($image['gallery4_default'] == '1') ? $image['position_default'] * 10 : $image['position_default'];
            if($image['gallery4_default'] != '1' && $image['gallery3_default'] != '1') $positionDefault = $image['position_default'] * 100;
            $_galleryReorder[$positionDefault] = $image;
        }
        ksort($_galleryReorder);
        $imgExplodedView = false;
        $imageUsed = 0;
        foreach ($_galleryReorder as $_image)
        {
            if($_image['gallery6_default'] == '1')
            {
                $imgExplodedView = true;
            }

            if($productForExplodedView && $imgExplodedView)
            {
                $images[] = array(
                    'value_id' => $_image['value_id'],
                    'url'      => Mage::getBaseUrl() . 'media/catalog/product' . $_image['file'],
                );
                $imageUsed++;
            }
            elseif(($bundled_product->getIsSingle() && $_image['gallery4_default'] == '1')
                || (!$bundled_product->getIsSingle() && !in_array($configurableProduct->getId(), $imageProductsCheck) && $_image['gallery3_default'] == '1')
                || ((!$bundled_product->getIsSingle() && in_array($configurableProduct->getId(), $imageProductsCheck) && $_image['gallery5_default'] == '1')))
            {
                if(!$productForExplodedView)
                {
                    $images[] = array(
                        'value_id' => $_image['value_id'],
                        'url'      => Mage::getBaseUrl() . 'media/catalog/product' . $_image['file'],
                    );
                    $imageUsed++;
                }
            }
        }

        if($bundleExplodedView != $imageUsed && $imgExplodedView)
        {
            for($i=0;$i<($bundleExplodedView - 1);$i++)
            {
                array_unshift($images, "");
            }
        }

        return $images;
    }

}