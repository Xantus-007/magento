<?php

class Monbento_Bundle_Model_Product_Type extends Mage_Bundle_Model_Product_Type {

    public function getSelectionsByBundleOptions($optionsParam, $product) {
        $options = array();
        $optionIds = array();
        $selections = array();
        foreach ($optionsParam as $key => $params) {
            $options[$key] = $params;
            $optionIds[] = $key;
        }
        if (count($optionIds)) {
            $_options = $this->getOptionsByIds($optionIds, $product);
            $_selections = $this->getSelectionsCollection($optionIds, $product);

            foreach ($_options as $key => $_option) {
                $optionTitle = explode('-', $_option->getTitle());
                foreach ($_selections as $_selection) {
                    $_attributes = $_selection->getAttributes();
                    $_imageType = $this->getCustomImageType($optionTitle[0]);
                    if ($_selection->getSelectionId() == $options[$_option->getId()]) {
                        $selections[$optionTitle[0]] = $_selection->getEntityId();
                    }
                }
            }
        }
        
        return $selections;
    }

    public function getSelectionsCollection($optionIds, $product = null) {
        $keyOptionIds = (is_array($optionIds) ? implode('_', $optionIds) : '');
        $key = $this->_keySelectionsCollection . $keyOptionIds;
        if (!$this->getProduct($product)->hasData($key)) {
            $selectionsCollection = Mage::getResourceModel('bundle/selection_collection')
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addAttributeToSelect(array('image_bundle_1', 'image_bundle_2', 'image_bundle_3', 'color_hexa', 'image_bundle_motif'), 'inner')
                    ->setFlag('require_stock_items', true)
                    ->setFlag('product_children', true)
                    ->setPositionOrder()
                    ->addStoreFilter($this->getStoreFilter($product))
                    ->addFilterByRequiredOptions()
                    ->setOptionIdsFilter($optionIds);

            $this->getProduct($product)->setData($key, $selectionsCollection);
        }
        return $this->getProduct($product)->getData($key);
    }

    public function getCustomImageFromUrl($w, $h = null) {
        $h = isset($h) ? $h : $w;
        $options = array();
        $optionIds = array();
        $selections = array();
        foreach ($_GET as $key => $params) {
            if (substr($key, 0, 14) == 'bundle-option-') {
                $options[substr($key, 14)] = $params;
                $optionIds[] = substr($key, 14);
            }
        }
        if (count($optionIds)) {
            $_options = $this->getOptionsByIds($optionIds, Mage::registry('current_product'));
            $_selections = $this->getSelectionsCollection($optionIds, Mage::registry('current_product'));
            foreach ($_options as $key => $_option) {
                $optionTitle = explode('-', $_option->getTitle());
                foreach ($_selections as $_selection) {
                    $_attributes = $_selection->getAttributes();
                    $_imageType = $this->getCustomImageType($optionTitle[0]);
                    if ($_selection->getSelectionId() == $options[$_option->getId()]) {
                        $selections[$optionTitle[0]] = $_selection->getEntityId();
                    }
                }
            }
            return $this->getCustomImage($selections, $w, $h);
        } else {
            return false;
        }
    }

    public function createFolder($folder) {
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
    }

    public function getBaseCustomImage($selections, $w, $h) {
        $image = $this->getCustomImage($selections, $w, $h);
        $_baseMediaDir = Mage::getBaseDir('media');
        $_custombaseDir = $_baseMediaDir . DS . 'custom';
        $_mediaDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $_customDir = $_mediaDir . 'custom';
        return str_replace($_customDir, $_custombaseDir, $image);
    }

    public function getCustomImage($selections, $w = null, $h = null) {
        $selections = array_filter($selections);
        $_baseMediaDir = Mage::getBaseDir('media');
        $_custombaseDir = $_baseMediaDir . DS . 'custom';
        $_mediaDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $_customDir = $_mediaDir . 'custom';

        $newFilename = md5(str_replace(array('&', '='), "", http_build_query($selections)));

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

        $newFilename = substr($newFilename, 0, 1) . DS . substr($newFilename, 1, 1) . DS . substr($newFilename, 2, 1) . DS . $newFilename . '.jpg';

        if (!file_exists($v . $size_folder . DS . $newFilename)) {

            switch (count($selections)) {
                case 6:
                    $_type = 2;
                    break;
                case 8:
                    $_type = 3;
                    break;
                default:
                    $_type = 1;
                    break;
            }
            $products = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('image_bundle_1')
                    ->addAttributeToSelect('image_bundle_2')
                    ->addAttributeToSelect('image_bundle_3')
                    ->addAttributeToFilter('entity_id', array('in' => array($selections)));

            $_images = array();
            $folderProductId = '3380';

            foreach ($products as $product) {
                foreach (array_keys($selections, $product->getId()) as $selection) {
                    $_imageType = $this->getCustomImageType($selection);
                    $_attributes = $product->getAttributes();
                    if ($_customImage = $_attributes[$_imageType]->getFrontend()->getValue($product)) {
                        $_images[] = $_customImage;
                    }
                }
                $folderProductId = Mage::getModel('bundle/product_type')->getParentIdsByChild($product->getId());
            }

            if(is_array($folderProductId)) $folderProductId = current($folderProductId);

            $_baseimage = imagecreatefromjpeg($_custombaseDir . DS . $folderProductId . DS . "blankcustombento" . $_type . ".jpg");
            list($ow, $oh, $type, $attr) = getimagesize($_custombaseDir . DS . $folderProductId . DS . "blankcustombento" . $_type . ".jpg");

            foreach ($_images as &$image) {
                if (file_exists($_baseMediaDir . DS . 'catalog/product' . DS . $image)) {
                    $_image = imagecreatefrompng($_baseMediaDir . DS . 'catalog/product' . DS . $image);
                    imagealphablending($_image, true);
                    imagesavealpha($_image, true);
                    imagecopy($_baseimage, $_image, 0, 0, 0, 0, $ow, $oh);
                    imagedestroy($_image);
                }
            }

            imagejpeg($_baseimage, $_custombaseDir . $size_folder . DS . $newFilename);

            if ($w && file_exists($_custombaseDir . $size_folder . DS . $newFilename)) {
                $_baseimage = new Varien_Image($_custombaseDir . $size_folder . DS . $newFilename);
                $_baseimage->resize($w, $h);
                $_baseimage->save($_custombaseDir . $size_folder, $newFilename);
            }
        }

        return $_customDir . $size_folder . DS . $newFilename;
    }

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

    public function getCustomImageType($option) {
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

}
