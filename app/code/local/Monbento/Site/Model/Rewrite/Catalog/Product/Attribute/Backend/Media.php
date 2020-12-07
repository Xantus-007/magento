<?php

class Monbento_Site_Model_Rewrite_Catalog_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Product_Attribute_Backend_Media
{
    
    /**
     * Load attribute data after product loaded
     *
     * @param Mage_Catalog_Model_Product $object
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = array();
        $value['images'] = array();
        $value['values'] = array();
        $localAttributes = array('label', 'position', 'disabled', 'gallery1', 'gallery2', 'gallery3', 'gallery4', 'gallery5', 'gallery6');

        foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
            foreach ($localAttributes as $localAttribute) {
                if (is_null($image[$localAttribute])) {
                    $image[$localAttribute] = $this->_getDefaultValue($localAttribute, $image);
                }
            }
            $value['images'][] = $image;
        }

        $object->setData($attrCode, $value);
    }
    
    public function afterSave($object)
    {
        if ($object->getIsDuplicate() == true) {
            $this->duplicate($object);
            return;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
            return;
        }

        $storeId = $object->getStoreId();

        $storeIds = $object->getStoreIds();
        $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;

        // remove current storeId
        $storeIds = array_flip($storeIds);
        unset($storeIds[$storeId]);
        $storeIds = array_keys($storeIds);

        $images = Mage::getResourceModel('catalog/product')
            ->getAssignedImages($object, $storeIds);

        $picturesInOtherStores = array();
        /*foreach ($images as $image) {
            $picturesInOtherStores[$image['filepath']] = true;
        }*/

        $toDelete = array();
        $filesToValueIds = array();
        foreach ($value['images'] as &$image) {
            if(!empty($image['removed'])) {
                if(isset($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if(!isset($image['value_id'])) {
                $data = array();
                $data['entity_id']      = $object->getId();
                $data['attribute_id']   = $this->getAttribute()->getId();
                $data['value']          = $image['file'];
                $image['value_id']      = $this->_getResource()->insertGallery($data);
            }

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = array();
            $data['value_id'] = $image['value_id'];
            $data['label']    = $image['label'];
            $data['position'] = (int) $image['position'];
            $data['disabled'] = (int) $image['disabled'];
            $data['gallery1'] = (int) $image['gallery1'];
            $data['gallery2'] = (int) $image['gallery2'];
            $data['gallery3'] = (int) $image['gallery3'];
            $data['gallery4'] = (int) $image['gallery4'];
            $data['gallery5'] = (int) $image['gallery5'];
            $data['gallery6'] = (int) $image['gallery6'];
            $data['store_id'] = (int) $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);
    }
    
    /**
     * Add image to media gallery and return new filename
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string                     $file              file path of image in file system
     * @param string|array               $mediaAttribute    code of attribute with type 'media_image',
     *                                                      leave blank if image should be only in gallery
     * @param boolean                    $move              if true, it will move source file
     * @param boolean                    $exclude           mark image as disabled in product page view
     * @return string
     */
    public function addImage(Mage_Catalog_Model_Product $product, $file,
        $mediaAttribute = null, $move = false, $exclude = true, $gallery1 = false, $gallery2 = false, $gallery3 = false, $gallery4 = false, $gallery5 = false, $gallery6 = false)
    {
        $file = realpath($file);

        if (!$file || !file_exists($file)) {
            Mage::throwException(Mage::helper('catalog')->__('Image does not exist.'));
        }

        Mage::dispatchEvent('catalog_product_media_add_image', array('product' => $product, 'image' => $file));

        $pathinfo = pathinfo($file);
        $imgExtensions = array('jpg','jpeg','gif','png');
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid image file type.'));
        }

        $fileName       = Mage_Core_Model_File_Uploader::getCorrectFileName($pathinfo['basename']);
        $dispretionPath = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
        $fileName       = $dispretionPath . DS . $fileName;

        $fileName = $this->_getNotDuplicatedFilename($fileName, $dispretionPath);

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->setAllowCreateFolders(true);
        $distanationDirectory = dirname($this->_getConfig()->getTmpMediaPath($fileName));

        try {
            $ioAdapter->open(array(
                'path'=>$distanationDirectory
            ));

            /** @var $storageHelper Mage_Core_Helper_File_Storage_Database */
            $storageHelper = Mage::helper('core/file_storage_database');
            if ($move) {
                $ioAdapter->mv($file, $this->_getConfig()->getTmpMediaPath($fileName));

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
            } else {
                $ioAdapter->cp($file, $this->_getConfig()->getTmpMediaPath($fileName));

                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
                $ioAdapter->chmod($this->_getConfig()->getTmpMediaPath($fileName), 0777);
            }
        }
        catch (Exception $e) {
            Mage::throwException(Mage::helper('catalog')->__('Failed to move file: %s', $e->getMessage()));
        }

        $fileName = str_replace(DS, '/', $fileName);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        $position = 0;
        if (!is_array($mediaGalleryData)) {
            $mediaGalleryData = array(
                'images' => array()
            );
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if (isset($image['position']) && $image['position'] > $position) {
                $position = $image['position'];
            }
        }

        $position++;
        $mediaGalleryData['images'][] = array(
            'file'     => $fileName,
            'position' => $position,
            'label'    => '',
            'disabled' => (int) $exclude,
            'gallery1' => (int) $gallery1,
            'gallery2' => (int) $gallery2,
            'gallery3' => (int) $gallery3,
            'gallery4' => (int) $gallery4,
            'gallery5' => (int) $gallery5,
            'gallery6' => (int) $gallery6
        );

        $product->setData($attrCode, $mediaGalleryData);

        if (!is_null($mediaAttribute)) {
            $this->setMediaAttribute($product, $mediaAttribute, $fileName);
        }

        return $fileName;
    }

    /**
     * Add images with different media attributes.
     * Image will be added only once if the same image is used with different media attributes
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $fileAndAttributesArray array of arrays of filename and corresponding media attribute
     * @param string $filePath path, where image cand be found
     * @param boolean $move if true, it will move source file
     * @param boolean $exclude mark image as disabled in product page view
     * @return array array of parallel arrays with original and renamed files
     */
    public function addImagesWithDifferentMediaAttributes(Mage_Catalog_Model_Product $product,
        $fileAndAttributesArray, $filePath = '', $move = false, $exclude = true, $gallery1 = true, $gallery2 = true, $gallery3 = true, $gallery4 = true, $gallery5 = true, $gallery6 = true) {

        $alreadyAddedFiles = array();
        $alreadyAddedFilesNames = array();

        foreach ($fileAndAttributesArray as $key => $value) {
            $keyInAddedFiles = array_search($value['file'], $alreadyAddedFiles, true);
            if ($keyInAddedFiles === false) {
                $savedFileName = $this->addImage($product, $filePath . $value['file'], null, $move, $exclude, $gallery1, $gallery2, $gallery3, $gallery4, $gallery5, $gallery6);
                $alreadyAddedFiles[$key] = $value['file'];
                $alreadyAddedFilesNames[$key] = $savedFileName;
            } else {
                $savedFileName = $alreadyAddedFilesNames[$keyInAddedFiles];
            }

            if (!is_null($value['mediaAttribute'])) {
                $this->setMediaAttribute($product, $value['mediaAttribute'], $savedFileName);
            }

        }

        return array('alreadyAddedFiles' => $alreadyAddedFiles, 'alreadyAddedFilesNames' => $alreadyAddedFilesNames);
    }

} // Class Mage_Catalog_Model_Product_Attribute_Backend_Media End
