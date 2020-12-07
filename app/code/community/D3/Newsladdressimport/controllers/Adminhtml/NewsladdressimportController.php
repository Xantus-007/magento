<?php

class D3_Newsladdressimport_Adminhtml_NewsladdressimportController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('newsladdressimport/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('newsladdressimport/newsladdressimport')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('newsladdressimport_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('newsladdressimport/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('newsladdressimport/adminhtml_newsladdressimport_edit'))
				->_addLeft($this->getLayout()->createBlock('newsladdressimport/adminhtml_newsladdressimport_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsladdressimport')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('newsladdressimport/newsladdressimport');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('newsladdressimport')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsladdressimport')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('newsladdressimport/newsladdressimport');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $newsladdressimportIds = $this->getRequest()->getParam('newsladdressimport');
        if(!is_array($newsladdressimportIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($newsladdressimportIds as $newsladdressimportId) {
                    $newsladdressimport = Mage::getModel('newsladdressimport/newsladdressimport')->load($newsladdressimportId);
                    $newsladdressimport->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($newsladdressimportIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function importAction($myID=0) {
		if( $this->getRequest()->getParam('id') > 0 || $myID > 0 ) {
			try {
				$model = Mage::getModel('newsladdressimport/newsladdressimport');
				 
				//$model->setId($this->getRequest()->getParam('id'))->delete();
				if($myID > 0){
					$id     = $myID;
				} else {
					$id     = $this->getRequest()->getParam('id');
				}
				$model  = Mage::getModel('newsladdressimport/newsladdressimport')->load($id);
				$myData = $model->getData();
				//Mage::getSingleton('adminhtml/session')->addSuccess($myData['content']);
				// db entry for backup listing
				try
		        {
		        	if(strpos($myData['content'],",")!==false){
		        		$myAddresses = explode(",",$myData['content']);
		        	} else {
		        		$myAddresses = explode("\r\n",$myData['content']);
		        	}
		        	foreach($myAddresses AS $value){
						if (Zend_Validate::is($value, 'EmailAddress')) {
		           			$q = "INSERT INTO `newsletter_subscriber` ( `subscriber_id` , `store_id` , `change_status_at` , `customer_id` , `subscriber_email` , `subscriber_status` , `subscriber_confirm_code` )
							  	VALUES ( NULL , '1', NOW( ) , '0', '".$value."', '1', '' );";
		           			Mage::getSingleton('core/resource')->getConnection('core_write')->query($q);
		           			//$status = Mage::getModel('newsletter/subscriber')->subscribe($myData['content']);
		           		}
		           	}
		        } catch (Exception $e)
		        {
		            Mage::log($e->getMessage());
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Error: '.$e->getMessage()));
		        }
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was imported'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
    
    public function massImportAction() {
        $newsladdressimportIds = $this->getRequest()->getParam('newsladdressimport');
        if(!is_array($newsladdressimportIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($newsladdressimportIds as $newsladdressimportId) {
                    //$newsladdressimport = Mage::getModel('newsladdressimport/newsladdressimport')->load($newsladdressimportId);
                    //$newsladdressimport->import();
                    //Mage::getSingleton('adminhtml/session')->addSuccess('<pre>'.print_r($newsladdressimport,true).'</pre>');
                    $this->importAction($newsladdressimportId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully imported', count($newsladdressimportIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $newsladdressimportIds = $this->getRequest()->getParam('newsladdressimport');
        if(!is_array($newsladdressimportIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($newsladdressimportIds as $newsladdressimportId) {
                    $newsladdressimport = Mage::getSingleton('newsladdressimport/newsladdressimport')
                        ->load($newsladdressimportId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($newsladdressimportIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'newsladdressimport.csv';
        $content    = $this->getLayout()->createBlock('newsladdressimport/adminhtml_newsladdressimport_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'newsladdressimport.xml';
        $content    = $this->getLayout()->createBlock('newsladdressimport/adminhtml_newsladdressimport_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}