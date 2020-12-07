<?php

class Dbm_Share_CommentController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions() {
        return array(post);
    }

    public function postAction()
    {
        $request = $this->getRequest();
        $session = Mage::getSingleton('core/session');
        $message = $request->getParam('message');
        $trans = Mage::helper('dbm_share');
        
        if($request->isPost() && strlen($message) > 0)
        {
            $api = Mage::getModel('dbm_share/api_v2');
            try {
                $currentCustomer = Mage::helper('dbm_customer')->getCurrentCustomer();
                
                if(Mage::helper('dbm_customer')->isValidProfile($currentCustomer))
                {
                    Mage::getModel('dbm_share/comment')->saveCommentForCurrentCustomer($request->getParam('id_element', null), $message);
                    $session->addSuccess($trans->__('Comment registered'));
                }
                else
                {
                    $session->addError($trans->__('You must have a nickname and a profile image to post elements on the club'));
                }
            } catch(Exception $err)
            {
                $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
                
                if(!$customer->getId())
                {
                    $session->addError($trans->__('You must be logged in to post a comment on the club'));
                }
                else
                {
                    $session->addError($err->getMessage());
                }
            }
        }

        $mask = Mage::app()->getStore()->getBaseUrl();
        $url = $this->_getRefererUrl();
        $url = str_replace($mask, '', $url);

        $finalUrl = Mage::getUrl($url, $params);
        $this->_redirectUrl($finalUrl);
    }
}