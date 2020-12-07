<?php

class Dbm_Customer_AccountController extends Dbm_Customer_Controller_Auth
{
    protected function _getPublicActions()
    {
        return array();
    }

    public function editAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index');
        
        $handles = array(
            'default',
            'customer_account',
            'share_default',
            'dbm_share_public_index_pepites_override',
            strtolower($this->getFullActionName())
        );

        $this->loadLayout($handles);
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('core/session');
        
        $this->renderLayout();
    }

    public function saveAction()
    {
        $trans = Mage::helper('dbm_share');
        $params = $this->getRequest()->getParams();
        $session = Mage::getSingleton('customer/session');
        $customerSession = Mage::getModel('dbm_customer/session');
        $success = false;
        $hasError = false;
        
        if(!strlen(trim($params['profile_nickname'])))
        {
            $hasError = true;
            $session->addError($trans->__('You must have a nickname'));
        }

        if(!$hasError && $this->getRequest()->isPost())
        {
            $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
            if((isset($params['profile_image_delete']) && $params['profile_image_delete'] == 1) 
                || (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0))
            {
                $oldImageUrl = Mage::getBaseDir('media').DS.Dbm_Customer_Helper_Data::MEDIA_FOLDER.$customer->getProfileImage();

                @unlink($oldImageUrl);
                $customer->setProfileImage('');
            }
            try {
                $this->_manageUpload($customer, 'profile_image');
            } catch (Exception $e) {
                $defaultCustomerData = Mage::helper('dbm_customer')->generateCustomerProfileData($customer);
                
		if(isset($_FILES['profile_image']) && !$customer->getProfileImage())
		{
		    $customer->setData('profile_image', $defaultCustomerData['profile_image']);
		}
	    }
            $customer->setData('profile_nickname', $params['profile_nickname']);
            $customer->setData('profile_url', $params['profile_url']);
            
            $customer->save();
            $success = true;
            $session->addSuccess($trans->__('Your profile is successfuly registered'));
            Mage::helper('dbm_customer')->updateCustomerStatus($customer);
        }
        
        if($params['goto-club'] == 1)
        {
            $this->_redirect('club/index/index');
        }
        elseif($customerSession->getIsMobile())
        {
            if($success)
            {
                Mage::app()->getFrontController()->getResponse()->setRedirect('monbento://loginSuccess');
            }
            else
            {
                $this->_redirect('*/*/edit');
            }
        }
        else
        {
            $this->_redirect('*/*/edit');
        }
    }

    protected function _manageUpload($customer, $param)
    {
        $customerId = $customer->getId();
        $path = Mage::getBaseDir('media') . DS . Dbm_Customer_Helper_Data::MEDIA_FOLDER . DS;
        $fileName = $_FILES[$param]['name'];
        $ext = substr($fileName, strrpos($fileName, '.'));
        $newName = time() . '-'.$customerId.$ext;
        $path2 = '';

        $path2 .= $customerId[0].DS;

        if(strlen($customerId) > 1)
        {
            $path2 .=$customerId[1].DS;
        }

        $file = new Varien_Io_File();
        $file->setAllowCreateFolders(true);
        $file->checkAndCreateFolder($path);

        $uploader = new Varien_File_Uploader($param);
        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
        $uploader->setAllowRenameFiles(true);
        $uploader->save($path.$path2, $newName);

        $customer->setData($param, DS.$path2.$uploader->getUploadedFileName());
    }
}
