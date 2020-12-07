<?php

class Dbm_Share_Adminhtml_CategoryController extends Dbm_Share_Controller_Upload
{
    public function indexAction()
    {
        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName())
        ));

        $gridContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_category_list_gridContainer');
        $this->_addContent($gridContainer);
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName())
        ));

        $formContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_category_edit');
        $this->_addContent($formContainer);
        $this->renderLayout();
    }

    public function saveAction()
    {
        $catModel = Mage::getModel('dbm_share/category');
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('dbm_share');

        $allowedLocales = Mage::helper('dbm_share')->getAllowedLocales();

        if(isset($params['id']) && $params['id'] > 0)
        {
            //$catModel->setId($params['id']);
            $catModel->load($params['id']);
        }

        $this->_manageUpload('image', 'image', $params, 0);
        $this->_manageUpload('image2', 'image2', $params, 1);

        $params['image']['value'] = basename($params['image']['value']);
        $params['image2']['value'] = basename($params['image2']['value']);
        
        foreach($allowedLocales as $locale)
        {
            if(!in_array($locale, array('en_ie'))) $catModel->setData('title_'.$locale, $params['title_'.$locale]);
            if(!in_array($locale, array('en_ie', 'pt_pt', 'ja_jp'))) $catModel->setData('meta_description_'.$locale, $params['meta_description_'.$locale]);
        }
        $catModel->setPosition($params['position']);
        
        if($catModel->save())
        {
            $images = array('', 2);
            
            foreach($images as $imageNum)
            {
                if(isset($params['image'.$imageNum])
                    && isset($params['image'.$imageNum]['value'])
                    && $params['image'.$imageNum]['delete'] != 1)
                {
                    $catModel->setData('image'.$imageNum, $params['image'.$imageNum]['value']);
                }
                elseif(isset($params['image'.$imageNum])
                    && isset($params['image'.$imageNum])
                    && $params['image'.$imageNum]['delete'] == 1)
                {
                    $catModel->setData('image'.$imageNum, '');
                    @unlink($helper->getCategoryImagePath(true).$params['image'.$imageNum]['value']);
                }
            }
            
            $catModel->save();

            $this->_getSession()->addSuccess('Enregistrement effectué');
        }
        else
        {
            $this->_getSession()->addError('Une erreur a eu lieu lors de l\'enregistrement');
        }

        $this->_redirect('*/*/', array('id' => $catModel->getId()));
    }

    public function deleteAction()
    {
        $catModel = Mage::getModel('dbm_share/category');
        $params = $this->getRequest()->getParams();

        if($catModel->load($params['id']))
        {
            $this->_getSession()->addSuccess('Suppression de la catégorie '.$catModel->getTitleFrFr());
            $catModel->delete();
        }

        $this->_redirect('*/*/');
    }

    public function _getUploadedSavePath($filename)
    {
        return Mage::helper('dbm_share')->getCategoryImagePath(true);
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
                $element = Mage::getModel('dbm_share/category')->load($id);

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

    protected function _massDelete(Dbm_Share_Model_Category $element)
    {
        if($element->getId() > 0)
        {
            $element->delete();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dbm_share/dbm_share_category');
    }
}