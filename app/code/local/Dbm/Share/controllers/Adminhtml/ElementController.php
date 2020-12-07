<?php

class Dbm_Share_Adminhtml_ElementController extends Dbm_Share_Controller_Upload
{
    public function _construct()
    {
        $type = $this->getRequest()->getParam('type', null);

        if($type && Mage::helper('dbm_share')->isTypeAllowed($type))
        {
            $this->_registerCurrentType($type);
        }
        
        $this->_publicActions[] = 'migrate';
        
        parent::_construct();
    }

    public function indexAction()
    {
        $this->loadLayout();

        
        $gridContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_element_list_gridContainer');
        $this->_addContent($gridContainer);

        $this->renderLayout();
    }

    public function newReceipeAction()
    {
        $this->_registerCurrentType(Dbm_Share_Model_Element::TYPE_RECEIPE);
        $this->_edit();
    }

    public function newPhotoAction()
    {
        $this->_registerCurrentType(Dbm_Share_Model_Element::TYPE_PHOTO);
        $this->_edit();
    }

    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        $element = Mage::getModel('dbm_share/element');

        if(isset($params['id']) && $element->load($params['id']))
        {
            $this->_registerCurrentType($element->getType());
        }

        $this->_edit();
    }

    public function saveAction()
    {
        $element = Mage::getModel('dbm_share/element');
        $params = $this->getRequest()->getParams();

        $isNew = !(isset($params['id']) && $params['id'] > 0 && $element->load($params['id']));

        $uploadResults = array();
        $uploadResults[] = $this->_manageUpload('photo', 'photo', $params, 0);

        $this->_saveElement($isNew, $params);
    }

    public function deleteAction()
    {
        $params = $this->getRequest()->getParams();
        $element = Mage::getModel('dbm_share/element')->load($params['id']);
        
        if($element->getId())
        {
            $element->delete();
        }
        
        $this->_redirect('*/*/');
    }

    protected function _saveElement($isNew, $params)
    {
        $element = Mage::getModel('dbm_share/element');
        $photo = Mage::getModel('dbm_share/photo');
        $shareHelper = Mage::helper('dbm_share');
        $customer = Mage::getModel('customer/customer')->load($params['id_customer']);

        if(!$isNew)
        {
            $element->load($params['id']);
        }

        if($customer->getId() > 0)
        {
            $element->setType($params['type']);
            $element->setShowInHome($params['show_in_home']);
            $element->setIdCustomer($params['id_customer']);

            if($element->getType() == Dbm_Share_Model_Element::TYPE_RECEIPE)
            {
                $element->setLevel($params['level']);
                $element->setPrice($params['price']);
                $element->setDuration($params['duration']);
                $element->setDurationUnit($params['duration_unit']);
                $element->setCookingDuration($params['cooking_duration']);
                $element->setCookingDurationUnit($params['cooking_duration_unit']);
            }

            $locales = $shareHelper->getAllowedLocales();
            foreach($locales as $locale)
            {
                $element->setData('title_'.$locale, $params['title_'.$locale]);
                if($element->getType() == Dbm_Share_Model_Element::TYPE_RECEIPE)
                {
                    $element->setData('description_'.$locale, $params['description_'.$locale]);
                    $element->setData('ingredients_legend_'.$locale, $params['ingredients_legend_'.$locale]);
                    $element->setData('ingredients_content_'.$locale, $params['ingredients_content_'.$locale]);
                }
            }

            $element->save();

            //Save categories
            $element->saveCategoryIds($params['categories']);

            //Save photo
            $params['photo']['value'] = basename($params['photo']['value']);

            if(isset($params['photo'])
                && isset($params['photo']['value'])
                && !empty($params['photo']['value'])
                && !isset($params['photo']['delete']))
            {
                $photoFilepath = Mage::helper('dbm_share')->getPhotoDir($params['photo']['value']).$params['photo']['value'];
                $isNewPhoto = false;
                if(file_exists($photoFilepath))
                {
                    $md5File = md5_file($photoFilepath);

                    //Load image from md5
                    $isNewPhoto = true;
                    if(!$isNew)
                    {
                        $testPhotos = Mage::getModel('dbm_share/photo')
                            ->getCollection()
                            ->addElementFilter($element)
                            ->addMd5Filter($md5File);

                        if(count($testPhotos) > 0)
                        {
                            $isNewPhoto = false;

                            //Delete uploaded file unless it has the same path as DB
                            if(strlen($params['photo']['value']) > 0
                                && $testPhotos->getFirstItem()->getFilename() != $params['photo']['value'])
                            {
                                @unlink($photoFilepath);
                                Mage::helper('dbm_share')->cleanPhotoPath($params['photo']['value']);
                            }
                        }
                        else
                        {
                            $element->flushPhotos();
                        }
                    }
                }

                //SAVE PHOTO
                if($isNewPhoto)
                {
                    $photoPath = $shareHelper->getPhotoDir($params['photo']['value']).$params['photo']['value'];
                    $coords = $shareHelper->getGpsCoords($photoPath);

                    $photoData = array(
                        'id_element' => $element->getId(),
                        'filename' => $params['photo']['value'],
                        'md5' => $md5File
                    );

                    if(is_array($coords))
                    {
                        $photoData['lat'] = $coords['lat'];
                        $photoData['lng'] = $coords['lng'];
                    }

                    $photo->setData($photoData);
                    $photo->save();
                }
            }
            elseif(isset($params['photo'])
                && $params['photo']['delete']
                && $params['photo']['delete'] == 1)
            {
                //DELETE PHOTO
                if(!$isNew)
                {
                    $element->flushPhotos();
                }
            }

            $this->_redirect('*/*/');
        }
        else
        {
            $this->_getSession()->addError('Le client choisi n\'existe pas');
            $this->_redirect('*/*/edit', array('id' => $params['id']));
        }
    }

    protected function _edit()
    {
        $this->loadLayout();

        $formContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_element_edit');
        $tabsContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_element_edit_tabs');

        $this->_addContent($formContainer);
        $this->_addLeft($tabsContainer);

        $this->renderLayout();
    }

    protected function _registerCurrentType($type)
    {
        Mage::register('dbm_share_current_type', strtolower(trim($type)));
    }

    protected function _getUploadedSavePath($filename)
    {
        return Mage::helper('dbm_share')->getPhotoDir($filename);
    }

    public function massactionAction()
    {
        $params = $this->getRequest()->getParams();

        $massAction = $params['massaction'];
        $action = $params['actioncallback'];

        if(is_array($massAction))
        {
            foreach($massAction as $id)
            {
                $element = Mage::getModel('dbm_share/element')->load($id);

                if($element->getId() > 0)
                {
                    switch($action)
                    {
                        case 'delete':
                            $this->_massDelete($element);
                            break;
                    }
                }
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    /** DEPRECATED
    public function migrateAction()
    {
        $helper = Mage::helper('dbm_share');
        $baseDir = Mage::getBaseDir('media').DS;
        $elementDir = $baseDir.Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.DS.'element'.DS;
        $tempPath = $baseDir.  Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.DS.'temp'.DS;
        
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        
        $io->createDestinationDir($tempPath);
        
        $dir01 = dir($elementDir);
        
        while($l01 = $dir01->read())
        {
            if($l01 != '.' && $l01 != '..' &&  $l01 != '.DS_Store')
            {
                $dir02 = dir($elementDir.$l01);
                
                while($l02 = $dir02->read())
                {
                    if($l02 != '.' && $l02 != '..' &&  $l02 != '.DS_Store')
                    {
                        $dir03 = dir($elementDir.$l01.DS.$l02);
                        
                        while($l03 = $dir03->read())
                        {
                            if($l03 != '.' && $l03 != '..' &&  $l03 != '.DS_Store')
                            {
                                $fullPath = $elementDir.$l01.DS.$l02.DS.$l03;
                                $filename = $l03;
                                if(file_exists($fullPath) && is_file($fullPath))
                                {
                                    echo'<pre>MOVING FROM '.$fullPath.' TO '.$tempPath.'</pre>';
                                    
                                    rename($fullPath, $tempPath.$filename);
                                    //$io->createDestinationDir($fullPath);
                                    //echo '<pre>'.$filename.' : '.$newPath.'</pre>';
                                    //$io->mv($tempPath.$filename, $newPath.$filename);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $tempDir = dir($tempPath);
        
        while($l = $tempDir->read())
        {
            if($l != '.' && $l != '..' &&  $l != '.DS_Store')
            {
                $newPath = $helper->getPhotoDir($l);
                $io->createDestinationDir($newPath);
                
                echo '<pre>FOUND FILES : '.$l.'</pre>';
                echo '<pre>MOVING FROM '.$tempPath.$l.' TO '.$newPath.$l.'</pre>';
                
                rename($tempPath.$l, $newPath.$l);
            }
        }
        
        exit();
        
    }
    */
    
    protected function _massDelete(Dbm_Share_Model_Element $element)
    {
        if($element->getId() > 0)
        {
            $element->delete();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dbm_share/dbm_share_elements');
    }
}