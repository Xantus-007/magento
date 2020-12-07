<?php
/**
 * Unirgy_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Unirgy_StoreLocator_Adminhtml_LocationController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('cms/ustorelocator');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Locations'), Mage::helper('adminhtml')->__('Store Locations'));
        $this->_addContent($this->getLayout()->createBlock('ustorelocator/adminhtml_location'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('cms/ustorelocator');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Locations'), Mage::helper('adminhtml')->__('Store Locations'));

        $this->_addContent($this->getLayout()->createBlock('ustorelocator/adminhtml_location_edit'))
            ->_addLeft($this->getLayout()->createBlock('ustorelocator/adminhtml_location_edit_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $model = Mage::getModel('ustorelocator/location')
                    //->addData($this->getRequest()->getParams())
                    ->setId($this->getRequest()->getParam('id'))

                    ->setTitle($this->getRequest()->getParam('title'))
                    ->setAddress($this->getRequest()->getParam('address'))
                    ->setNotes($this->getRequest()->getParam('notes'))
                    ->setPhoto($this->getRequest()->getParam('photo'))
                    ->setLongitude($this->getRequest()->getParam('longitude'))
                    ->setLatitude($this->getRequest()->getParam('latitude'))
                    ->setAddressDisplay($this->getRequest()->getParam('address_display'))
                    ->setNotes($this->getRequest()->getParam('notes'))
                    ->setWebsiteUrl($this->getRequest()->getParam('website_url'))
                    ->setPhone($this->getRequest()->getParam('phone'))
                    ->setUdropshipVendor($this->getRequest()->getParam('udropship_vendor'))
                ;
                //photo file
               // var_dump($_FIL);
                
					if(isset($_FILES['product_types']['name']) and (file_exists($_FILES['product_types']['tmp_name']))) {
						try {	
						
						$uploader = new Varien_File_Uploader('product_types');
					    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
		
		    			$uploader->setAllowRenameFiles(false);
		
		    			// setAllowRenameFiles(true) -> move your file in a folder the magento way
		    			// setAllowRenameFiles(true) -> move your file directly in the $path folder
					    $uploader->setFilesDispersion(false);
		    			$path = Mage::getBaseDir('media') . DS ;
		
					    $uploader->save($path, $_FILES['product_types']['name']);
						//var_dump($data);
		    			$data['product_types'] = $_FILES['product_types']['name']; 
							} catch (Exception $e) {
			    	            $this->_getSession()->addException($e, Mage::helper('ustorelocator')->__('Error uploading image. Please try again later.'));
					        }
					     $data['product_types'] = $_FILES['product_types']['name'];   
				    }
					else
					{
						if(isset($data['product_types']['delete']) && $data['product_types']['delete'] == 1)
							$data["product_types"]="";
						else
							$data["product_types"]="";
					}
				
				Mage::log("Je suis dans LocationController, mon data ".$data['product_types'], null, "specifique.log");
             	
				
				$model->setProductTypes($data['product_types']);   
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store location was successfully saved'));

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('ustorelocator/location');
                /* @var $model Mage_Rating_Model_Rating */
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store location was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('cms/ustorelocator');
    }

    protected function _validateSecretKey()
    {
        if ($this->getRequest()->getActionName()=='updateEmptyGeoLocations') {
            return true;
        }
        return parent::_validateSecretKey();
    }

    public function updateEmptyGeoLocationsAction()
    {
        set_time_limit(0);
        ob_implicit_flush();
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        $collection->getSelect()->where('latitude=0');
        foreach ($collection as $loc) {
            echo $loc->getTitle()."<br/>";
            $loc->save();
        }
        exit;
    }
}
