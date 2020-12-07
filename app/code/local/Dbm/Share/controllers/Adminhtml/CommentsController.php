<?php

/**
 * Description of CommentsController
 *
 * @author dlote
 */
class Dbm_Share_Adminhtml_CommentsController extends Dbm_Share_Controller_Upload{
    public function indexAction()
    {
        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName())
        ));

        $gridContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_comments_list_gridContainer');
        $this->_addContent($gridContainer);
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName())
        ));

        $formContainer = $this->getLayout()->createBlock('dbm_share/adminhtml_comments_edit');
        $this->_addContent($formContainer);
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        $comModel = Mage::getModel('dbm_share/comment');
        $params = $this->getRequest()->getParams();

        if(isset($params['id']) && $params['id'] > 0)
        {
            $comModel->load($params['id']);
        }

        if(isset($params['message']) && $params['message'] != ''){
            $comModel->setMessage($params['message']);
        }
        if(isset($params['status']) && $params['status'] != ''){
            $comModel->setStatus($params['status']);
        }
        
        $comModel->save();
        
        $this->_redirect('*/*/', array('id' => $comModel->getId()));
    }

    
    public function deleteAction()
    {
        $comModel = Mage::getModel('dbm_share/comments');
        $params = $this->getRequest()->getParams();

        if($comModel->load($params['id']))
        {
            $this->_getSession()->addSuccess('Suppression du commentaire');
            $comModel->delete();
        }

        $this->_redirect('*/*/');
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
                $element = Mage::getModel('dbm_share/comment')->load($id);

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
    
    protected function _massDelete(Dbm_Share_Model_Comment $element)
    {
        if($element->getId() > 0)
        {
            $element->delete();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dbm_share/dbm_share_comments');
    }
}

