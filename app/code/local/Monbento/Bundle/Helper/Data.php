<?php

class Monbento_Bundle_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomImage($selections, $w = null, $h = null, $view = 1)
	{
        $selections = array_filter($selections);

        $_baseMediaDir = Mage::getBaseDir('media');
        $_custombaseDir = $_baseMediaDir . DS . 'custom';
        $_mediaDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $_customDir = $_mediaDir . 'custom';

        $optionsSelected = array();

        foreach ($selections as $optionKey => $productId)
        {
        	if($optionKey == 'bundle-id')
        	{
        		$bundleProduct = Mage::getModel('catalog/product')->load($productId);

        		$_gallery = $bundleProduct->getMediaGallery('images');
                $galleryChoice = ($bundleProduct->getIsSingle()) ? 'gallery4_default' : 'gallery3_default';
                $_galleryReorder = array();
                foreach ($_gallery as $image)
                {
                    $_galleryReorder[$image['position_default']] = $image;
                }
                ksort($_galleryReorder);
                foreach ($_galleryReorder as $_image)
                {
                	if($_image[$galleryChoice] == '1' && $_image['gallery'.$view.'_default'] == '1')
                	{
                        $ext = pathinfo($_baseMediaDir . DS . 'catalog/product' . $_image['file'], PATHINFO_EXTENSION);
                        if($ext == 'jpg')
                        {
                            $_baseimage = imagecreatefromjpeg($_baseMediaDir . DS . 'catalog/product' . $_image['file']);
                        }
                        else
                        {
                            $_baseimage = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . $_image['file']);
                            imagealphablending($_baseimage, true);
                            imagesavealpha($_baseimage, true);
                            //Code for convert png to jpeg
                            /*if(file_exists($_baseMediaDir . DS . 'catalog/product' . str_replace('.png', '-jpg-conversion.jpg', $_image['file'])))
                            {
                                $_baseimage = imagecreatefromjpeg($_baseMediaDir . DS . 'catalog/product' . str_replace('.png', '-jpg-conversion.jpg', $_image['file']));
                            }
                            else
                            {
                                $_tmpbaseimage = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . $_image['file']);
                                $bg = imagecreatetruecolor(imagesx($_tmpbaseimage), imagesy($_tmpbaseimage));
                                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                                imagealphablending($bg, TRUE);
                                imagecopy($bg, $_tmpbaseimage, 0, 0, 0, 0, imagesx($_tmpbaseimage), imagesy($_tmpbaseimage));
                                imagedestroy($_tmpbaseimage);
                                imagejpeg($bg, $_baseMediaDir . DS . 'catalog/product' . str_replace('.png', '-jpg-conversion.jpg', $_image['file']));
                                $_baseimage = imagecreatefromjpeg($_baseMediaDir . DS . 'catalog/product' . str_replace('.png', '-jpg-conversion.jpg', $_image['file']));
                                imagedestroy($bg);
                            }*/
                        }
                		list($ow, $oh, $type, $attr) = getimagesize($_baseMediaDir . DS . 'catalog/product' . $_image['file']);
                		break;
                	}
                }
        	}
        	else
        	{
        		$optionId = explode('-', $optionKey);
        		$optionsSelected[$optionId[2]] = $productId;
        	}
        }

		$newFilename = md5(str_replace(array('&', '='), "", http_build_query($optionsSelected))).$view;

        if ($w) {
            $h = isset($h) ? $h : $w;
            $size_folder = DS . $w . 'x' . $h;
        } else {
            $size_folder = '';
        }

        // Create size Folder
        $this->createFolder($_custombaseDir . $size_folder);

        // Create Md5 Folders
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1));
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1));
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1) . DS . substr($newFilename, 2, 1));

        $newFilename = substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1) . DS . substr($newFilename, 2, 1) . DS . $newFilename . '.'.$ext;

        if (!file_exists($v . $size_folder . DS . $newFilename))
        {
            $selectionCollection = $bundleProduct->getTypeInstance(true)->getSelectionsCollection($bundleProduct->getTypeInstance(true)->getOptionsIds($bundleProduct), $bundleProduct);

            $imageProductsCheck = array();
            $_images = array();

            foreach($selectionCollection as $option)
            {
                $optionId = $option->getOptionId();
                $product = Mage::getModel('catalog/product')->load($optionsSelected[$optionId]);

                $_gallerySimple = $product->getMediaGallery('images');
                $galleryChoice = ($bundleProduct->getIsSingle()) ? 'gallery4_default' : 'gallery3_default';
                if(!$bundleProduct->getIsSingle() && in_array($option->product_id, $imageProductsCheck)) $galleryChoice = 'gallery5_default';
                $_galleryReorder = array();
                $productForExplodedView = false;
                foreach ($_gallerySimple as $image)
                {
                    $positionDefault = ($image['gallery4_default'] == '1') ? $image['position_default'] * 10 : $image['position_default'];
                    if($image['gallery4_default'] != '1' && $image['gallery3_default'] != '1') $positionDefault = $image['position_default'] * 100;
                    $_galleryReorder[$positionDefault] = $image;
                    if(strpos($bundleProduct->getSku(), 'square') !== false && !$bundleProduct->getIsSingle() && $image['gallery6_default'] == '1')
                    {
                        $productForExplodedView = true;
                    }
                }
                ksort($_galleryReorder);
                foreach ($_galleryReorder as $_image)
                {
                    if((($_image[$galleryChoice] == '1' && !$productForExplodedView) || ($_image[$galleryChoice] == '1' && $productForExplodedView && $_image['gallery6_default'] == '1') || ($productForExplodedView && $_image['gallery6_default'] == '1' && $_image['gallery3_default'] != '1' && $_image['gallery4_default'] != '1')) && $_image['gallery'.$view.'_default'] == '1')
                    {
                        $_images[] = $_image['file'];
                    }
                }

                $imageProductsCheck[] = $option->product_id;
            }

            foreach ($_images as $imageSimple)
            {
                if (file_exists($_baseMediaDir . DS . 'catalog/product' . $imageSimple)) {
                    $_image = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . $imageSimple);
                    imagealphablending($_image, true);
                    imagesavealpha($_image, true);
                    imagecopy($_baseimage, $_image, 0, 0, 0, 0, $ow, $oh);
                    imagedestroy($_image);
                }
            }

            if($ext == 'jpg')
            {
                imagejpeg($_baseimage, $_custombaseDir . $size_folder . DS . $newFilename);
            }
            else
            {
                imagepng($_baseimage, $_custombaseDir . $size_folder . DS . $newFilename);
            }

            if ($w && file_exists($_custombaseDir . $size_folder . DS . $newFilename)) {
                $_baseimage = new Varien_Image($_custombaseDir . $size_folder . DS . $newFilename);
                if($ext != 'jpg') $_baseimage->keepTransparency(true);
                $_baseimage->resize($w, $h);
                $_baseimage->save($_custombaseDir . $size_folder, $newFilename);
            }
        }

        return array("file" => $_custombaseDir . $size_folder . DS . $newFilename, "url" => $_customDir . $size_folder . DS . $newFilename);
    }

    public function getCustomImageFromImageIds($params, $w = null, $h = null, $view = 1)
	{
        $imageIds = array_filter(explode(',', $params['image-ids']));
        array_unshift($imageIds, $params['base-img']);
        $_baseMediaDir = Mage::getBaseDir('media');
        $_custombaseDir = $_baseMediaDir . DS . 'custom';
        $_mediaDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $_customDir = $_mediaDir . 'custom';

		$newFilename = md5(implode(',', $imageIds));

        if ($w) {
            $h = isset($h) ? $h : $w;
            $size_folder = DS . $w . 'x' . $h;
        } else {
            $size_folder = '';
        }

        // Create size Folder
        $this->createFolder($_custombaseDir . $size_folder);

        // Create Md5 Folders
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1));
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1));
        $this->createFolder($_custombaseDir . $size_folder . DS . substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1) . DS . substr($newFilename, 2, 1));

        $newFilename = substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1) . DS . substr($newFilename, 2, 1) . DS . $newFilename . '.png';

        if (!file_exists($_custombaseDir . $size_folder . DS . $newFilename))
        {
            $_baseimage = null;
            $_images = array();

            $imageModel = Mage::getResourceModel('catalog/product_attribute_backend_media');
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

            $select = $adapter->select()
                ->from(
                    array('main'=>$imageModel->getMainTable()),
                    array('value_id', 'value AS file', 'product_id' => 'entity_id')
                )
                ->joinLeft(
                    array('value' => $imageModel->getTable(Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::GALLERY_VALUE_TABLE)),
                    $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$storeId),
                    array('label','position','disabled')
                )
                ->joinLeft( // Joining default values
                    array('default_value' => $imageModel->getTable(Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::GALLERY_VALUE_TABLE)),
                    'main.value_id = default_value.value_id AND default_value.store_id = 0',
                    array(
                        'label_default' => 'label',
                        'position_default' => 'position',
                        'disabled_default' => 'disabled'
                    )
                )
                ->where('main.value_id in (?)', $imageIds);

            $result = $adapter->fetchAll($select);
            foreach ($result as $imageData) {
                if ($imageData['value_id'] == $params['base-img']) {
                    $_baseimage = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . $imageData['file']);
                    imagealphablending($_baseimage, true);
                    imagesavealpha($_baseimage, true);
                    list($biw, $bih, $type, $attr) = getimagesize($_baseMediaDir . DS . 'catalog/product' . $imageData['file']);
                    continue;
                }
                $_images[] = $imageData['file'];
            }

            foreach ($_images as $imageSimple)
            {
                if (file_exists($_baseMediaDir . DS . 'catalog/product' . $imageSimple)) {
                    $_image = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . $imageSimple);
                    list($ow, $oh, $type, $attr) = getimagesize($_baseMediaDir . DS . 'catalog/product' . $imageSimple);
                    imagealphablending($_image, true);
                    imagesavealpha($_image, true);
                    imagecopyresized($_baseimage, $_image, 0, 0, 0, 0, $biw, $bih, $ow, $oh);
                    imagedestroy($_image);
                }
            }

            imagepng($_baseimage, $_custombaseDir . $size_folder . DS . $newFilename);

            if ($w && file_exists($_custombaseDir . $size_folder . DS . $newFilename)) {
                $_baseimage = new Varien_Image($_custombaseDir . $size_folder . DS . $newFilename);
                $_baseimage->keepTransparency(true);
                $_baseimage->resize($w, $h);
                $_baseimage->save($_custombaseDir . $size_folder, $newFilename);
            }
        }

        return array("file" => $_custombaseDir . $size_folder . DS . $newFilename, "url" => $_customDir . $size_folder . DS . $newFilename);
    }

    public function getCustomImageFromUrl($width = null, $height = null, $view  = 1)
    {
        $params = Mage::app()->getRequest()->getParams();
        $selections = array();
        foreach ($params as $key => $value)
        {
            if($key == 'id')
            {
                $selections['bundle-id'] = $value;
            }
            else
            {
                $selections[$key] = $value;
            }
        }

        if(count($selections) <= 1) return Mage::helper('catalog/image')->init(Mage::registry('current_product'), 'image');

        $imageCustom = $this->getCustomImage($selections, $width, $height, $view);
        return $imageCustom['url'];
    }

    public function getSelectionsByBundleOptions($optionsParam, $product)
    {
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

        $matiereId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'matiere');
        $colorId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'color');

        $selections = array();
        $selections['bundle-id'] = $product->getId();

        foreach($selectionCollection as $option)
        {
            $configurableProduct = Mage::getModel('catalog/product')->load($option->product_id);
            $configurableProductItems = $configurableProduct->getTypeInstance(true)->getUsedProducts(null, $configurableProduct);

            foreach($configurableProductItems as $product)
            {
                if($optionsParam['super_attribute'][$option->option_id][$matiereId] == $product->getMatiere() && $optionsParam['super_attribute'][$option->option_id][$colorId] == $product->getColor())
                {
                    $selections['bundle-option-'.$option->option_id] = $product->getId();
                }
            }
        }

        return $selections;
    }

    public function getOptionsList($optionsParam, $_product)
    {
        $selectionCollection = $_product->getTypeInstance(true)->getSelectionsCollection($_product->getTypeInstance(true)->getOptionsIds($_product), $_product);

        $options = array();

        foreach ($selectionCollection as $option) {
            $configurableProduct = Mage::getModel('catalog/product')->load($option->product_id);
            if ($configurableProduct->getTypeId() === 'configurable') {
                $configurableProductItems = $configurableProduct->getTypeInstance(true)->getUsedProducts(null, $configurableProduct);
                $basePrice = $configurableProduct->getFinalPrice();

                foreach ($configurableProductItems as $product) {
                    if ($optionsParam['bundle-option-' . $option->option_id] == $product->getId()) {
                        $priceOption = max(0, ($product->getFinalPrice() - $basePrice));

                        $options[] = array(
                            "name" => $product->getName(),
                            "matiere" => $product->getAttributeText('matiere'),
                            "color" => $product->getAttributeText('color'),
                            "priceOption" => $priceOption
                        );
                    }
                }
            }
        }

        return $options;
    }

    public function createFolder($folder)
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
    }
}
