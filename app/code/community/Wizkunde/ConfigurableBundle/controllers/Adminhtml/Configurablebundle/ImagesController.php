<?php

class Wizkunde_ConfigurableBundle_Adminhtml_Configurablebundle_ImagesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();

        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('configurablebundle/image');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This scope no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Composite Image'));

        $data = Mage::getSingleton('adminhtml/session')->getAttributeData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $model->setProductSku($this->getProducts($model->getId()));

        $model->setExistingImages($this->getImages($model->getId()));

        Mage::register('configurablebundle', $model);
        Mage::getSingleton('adminhtml/session')->setProductsselectorData($model->getData());

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Composite Image') : $this->__('New Composite Image'), $id ? $this->__('Edit Composite Image') : $this->__('New Composite Image'))
            ->_addContent($this->getLayout()->createBlock('configurablebundle/adminhtml_images_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    protected function getProducts($imageId)
    {
        $collection = Mage::getResourceModel('configurablebundle/image_collection_product');

        $collection->addFieldToFilter('image_id', array('eq' => $imageId));

        $products = array();

        $productModel = Mage::getModel('catalog/product');

        foreach($collection as $product) {
            $productModel->load($product->getProductId());
            $products[] = $productModel->getSku();
        }

        if(count($products) > 0) {
            return implode(', ', $products);
        }

        return '';
    }

    protected function getImages($imageId)
    {
        $collection = Mage::getResourceModel('configurablebundle/image_collection_image');

        $collection->addFieldToFilter('image_id', array('eq' => $imageId));

        $images = array();

        foreach($collection as $i => $image) {
            $images[$image->getId()] = $image->getMain();
        }

        return $images;
    }

    protected function uploadFile($fileData)
    {
        $path = Mage::getBaseDir('media') . DS . 'configurablebundle' . DS;
        $uploader = new Varien_File_Uploader($fileData);
        $uploader->setAllowedExtensions(array('jpg','png','gif'));
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $destFile = $path.$fileData['name'];
        $fileInfo = pathinfo($destFile);
        $filename = uniqid(rand(), true) . '.' . $fileInfo['extension'];

        $uploader->save($path, $filename);
        return $filename;
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('configurablebundle/image');

            // Process images first
            if(isset($_FILES['images_0'])) {
                foreach($_FILES['images_0']['name'] as $key => $mainImage) {
                    $fileData = array(
                        'name'      => $mainImage,
                        'tmp_name'  => $_FILES['images_0']['tmp_name'][$key]
                    );

                    $postData['images_0'][$key]= $this->uploadFile($fileData);


                    $fileData = array(
                        'name'      => $_FILES['images_1']['name'][$key],
                        'tmp_name'  => $_FILES['images_1']['tmp_name'][$key]
                    );

                    $postData['images_1'][$key]= $this->uploadFile($fileData);

                    $fileData = array(
                        'name'      => $_FILES['images_2']['name'][$key],
                        'tmp_name'  => $_FILES['images_2']['tmp_name'][$key]
                    );

                    $postData['images_2'][$key]= $this->uploadFile($fileData);
                }
            }

            $postData['products'] = array();
            if(isset($postData['product_sku'])) {
                if(strpos($postData['product_sku'], ',') === -1) {
                    $skus = explode(',', $postData['product_sku']);

                    foreach($skus as $sku) {
                        $postData['products'][] = Mage::getModel('catalog/product')->getIdBySku(trim($sku));
                    }
                }
            }

            $model->setName($postData['name']);

            try {
                $model->save();

                // Save Images
                foreach($postData['images_0'] as $key => $image) {
                    $imageModel = Mage::getSingleton('configurablebundle/image_image');

                    $imageModel->setData(
                        array(
                            'image_id' => $model->getId(),
                            'path' => '/',
                            'sort' => $postData['images'][$key],
                            'main' => $image,
                            'thumbnail' => $postData['images_1'][$key],
                            'small_thumbnail' => $postData['images_2'][$key]
                        )
                    );

                    $imageModel->save();
                }

                // Save Products
                foreach($postData['products'] as $productId) {
                    $productModel = Mage::getSingleton('configurablebundle/image_product');

                    $productModel->setData(
                        array(
                            'image_id' => $model->getId(),
                            'product_id' => $productId
                        )
                    );

                    $productModel->save();
                }


                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The scope has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this identity provider.'));
            }

            Mage::getSingleton('adminhtml/session')->setAttributeData($postData);
            $this->_redirectReferer();
        }
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('configurablebundle/wizkunde_configurablebundle_image')
            ->_title($this->__('Wizkunde Composite Image'))->_title($this->__('Composite Images'))
            ->_addBreadcrumb($this->__('Composite Images'), $this->__('Composite Images'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('configurablebundle/wizkunde_configurablebundle_images');
    }
}