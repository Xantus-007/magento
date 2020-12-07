<?php

require_once str_replace('//', '/community/', Mage::getModuleDir('controllers', 'Unirgy_StoreLocator').DS.'Adminhtml/LocationController.php');

class Dbm_Store_Adminhtml_StorelocationController extends Unirgy_StoreLocator_Adminhtml_LocationController
{
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
                    ->setType($this->getRequest()->getParam('type'))
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
}
