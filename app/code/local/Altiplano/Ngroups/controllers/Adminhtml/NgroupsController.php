<?php

class Altiplano_Ngroups_Adminhtml_NgroupsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('ngroups/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

        public function oldgridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('ngroups/adminhtml_oldsubscriber_gridreload')->toHtml()
            );
        }
        public function gridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('ngroups/adminhtml_subscriber_gridreload')->toHtml()
            );
        }

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('ngroups/ngroups')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('ngroups_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('ngroups/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('ngroups/adminhtml_ngroups_edit'))
				->_addLeft($this->getLayout()->createBlock('ngroups/adminhtml_ngroups_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ngroups')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
            
		if ($data = $this->getRequest()->getPost()) {


                        $data['customers'] = $this->getRequest()->getParam('customers');

                        $deletecustomers = $this->getRequest()->getParam('deletecustomers');
                        $deletecustomers = preg_split('[,]',$deletecustomers);
                        
                        try {
                            if ($this->getRequest()->getParam('id') > 0)
                                $info = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $this->getRequest()->getParam('id'))->toArray();
                            else{
                            }
                            if (isset($info['items'][0]['customers'])){
                                $customers = $info['items'][0]['customers'];
                            }else
                                $customers = "";

                            Mage::getResourceModel('ngroups/ngroups')->unsetSubscribers($this->getRequest()->getParam('id'), $deletecustomers);

                            $customersArray = preg_split('[,]', $customers);
                            $customersArray = array_unique($customersArray);

                            foreach ($deletecustomers as $subscriber){

                                $number = array_search($subscriber, $customersArray);
                                unset($customersArray[$number]);

                            }
                            $comma_separated = implode(",", $customersArray);
                            Mage::getModel('ngroups/ngroups')->setData(array('customers'=>$comma_separated))->setId($this->getRequest()->getParam('id'))->save();

                           
                        } catch (Exception $e) {
//                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        }

                        $prevModel = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $this->getRequest()->getParam('id'))->toArray();

                        if (isset($prevModel['totalRecords']) && $prevModel['totalRecords'] > 0)
                            $users = $prevModel['items'][0]['customers'];
                        else
                            $users = "";

                        $data['customers'] = $data['customers'].','.$users;
                        $data['emails'] = nl2br($data['emails']);
                        $newEmString = "";
                        if (isset($data['emails']) && $data['emails']!="") {
                            $mails = explode('<br />',$data['emails']);

                            foreach ($mails as $mail){
								$mail = trim($mail);

                                try {
                                    if (!Zend_Validate::is($mail, 'EmailAddress')) {
                                     
                                    }
                                    $status = Mage::getModel('newsletter/subscriber')->subscribe($mail);
                                    if ($status > 0){
                                        $user = Mage::getModel('newsletter/subscriber')->loadByEmail($mail);
                                        $id = $user->getId();
					$user->confirm($user->getCode());
                                        $newEmString .= $id.",";
                                    }
                                    
                                }
                                catch (Mage_Core_Exception $e) {
                                }
                                catch (Exception $e) {
                                }
                                
                            }
                        }
                        $data['customers'] = $data['customers'].",".$newEmString;

                        $model = Mage::getModel('ngroups/ngroups');

                        $model->setData($data)
				->setId($this->getRequest()->getParam('id'));
                        $modelData = $model->getData();


                        try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();

                                Mage::getResourceModel('ngroups/ngroups')->setSubscribers($this->getRequest()->getParam('id'), $data['customers']);

                        
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ngroups')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ngroups')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('ngroups/ngroups');
				 
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
        $ngroupsIds = $this->getRequest()->getParam('ngroups');
        if(!is_array($ngroupsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ngroupsIds as $ngroupsId) {
                    $ngroups = Mage::getModel('ngroups/ngroups')->load($ngroupsId);
                    $ngroups->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ngroupsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massUnsubscribeAction() {
        $subscribers = $this->getRequest()->getParam('subscriber');
        if(!is_array($subscribers)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                if ($this->getRequest()->getParam('id') > 0)
                    $info = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $this->getRequest()->getParam('id'))->toArray();
                else{
                }
                if (isset($info['items'][0]['customers'])){
                    $customers = $info['items'][0]['customers'];
                }else
                    $customers = "";

                Mage::getResourceModel('ngroups/ngroups')->unsetSubscribers($this->getRequest()->getParam('id'), $subscribers);

                $customersArray = preg_split('[,]', $customers);
                $customersArray = array_unique($customersArray);

                foreach ($subscribers as $subscriber){

                    $number = array_search($subscriber, $customersArray);
                    unset($customersArray[$number]);

                    //$customers = str_replace($subscriber.',', '', $customers);
         
                }
                $comma_separated = implode(",", $customersArray);
                Mage::getModel('ngroups/ngroups')->setData(array('customers'=>$comma_separated))->setId($this->getRequest()->getParam('id'))->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d subscribers were successfully deleted from group', count($subscribers)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit', array('id'=>$this->getRequest()->getParam('id')));
    }

	
    public function massStatusAction()
    {
        $ngroupsIds = $this->getRequest()->getParam('ngroups');
        if(!is_array($ngroupsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($ngroupsIds as $ngroupsId) {
                    $ngroups = Mage::getSingleton('ngroups/ngroups')
                        ->load($ngroupsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ngroupsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'ngroups.csv';
        $content    = $this->getLayout()->createBlock('ngroups/adminhtml_ngroups_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'ngroups.xml';
        $content    = $this->getLayout()->createBlock('ngroups/adminhtml_ngroups_grid')
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