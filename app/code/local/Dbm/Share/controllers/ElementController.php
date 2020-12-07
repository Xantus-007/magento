<?php

class Dbm_Share_ElementController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions()
    {
        return array('post');
    }

    public function postAction()
    {
        $trans = Mage::helper('dbm_share');
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        
        if($this->getRequest()->isPost())
        {
            $type = $this->getRequest()->getParam('type', null);
            $storeId = Mage::app()->getStore()->getId();
            $locale = current(Mage::helper('dbm_share')->getDefaultLocaleForStoreId($storeId));
            $element = Mage::getModel('dbm_share/element');
            $session = Mage::getSingleton('core/session');
            
            try {
                switch($type)
                {
                    case Dbm_Share_Model_Element::TYPE_PHOTO:
                    case Dbm_Share_Model_Element::TYPE_RECEIPE:
                        $elementData = $this->getElementData();
                        
                        try {
                            $result = $element->saveElementFromApi($type, $customer, $elementData);
                            
                        } catch (Exception $ex) {
                            if(!$customer->getId())
                            {
                                Mage::throwException($trans->__('You must be logged in to post elements on the club'));
                            }
                            else
                            {
                                Mage::throwException($ex->getMessage());
                            }
                        }

                        if($result)
                        {
                            $session->addSuccess('Élément ajouté avec succès');
                            $this->_redirect('*/index/detail', array('id' => $result));
                        }

                        break;
                    default:
                        throw new Exception($trans->__('This element cannot be added'));
                        break;
                }
            } catch (Exception $err)
            {
                $session = $session->addError($err->getMessage());
                $mask = Mage::app()->getStore()->getBaseUrl();
                $url = $this->_getRefererUrl();
                $url = str_replace($mask, '', $url);

                $getParams = $this->getRequest()->getParams();
                $params = array();
                $params['_query'] = $getParams;
                //$params['_absolute'] = true;
                //$params['_direct'] = 'http://www.google.com';

                $finalUrl = Mage::getUrl($url, $params);
                $this->_redirectUrl($finalUrl);
            }
        }
    }

    protected function getElementData()
    {
        if($_FILES['photo']['tmp_name'])
        {
            $photoData = file_get_contents($_FILES['photo']['tmp_name']);
        }
        
        $result = new stdClass();
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('dbm_share');
        
        foreach($params as $name => $param)
        {
            $result->{$name} = $param;
        }

        $result->categories = array($params['categories']);

        //Localized fields
        $fields = Mage::helper('dbm_share')->getLocalizedFields();
        
        foreach($fields as $field)
        {
            if(isset($params[$field]) && is_array($params[$field]))
            {
                $tmpArray = array();
                
                foreach($params[$field] as $locale => $value)
                {
                    if($helper->isLocaleAllowed($locale))
                    {
                        $tmpObj = new stdClass();
                        $tmpObj->key = $locale;
                        $tmpObj->value = $value;
                        $tmpArray[] = $tmpObj;
                    }
                }
                
                $result->{$field} =$tmpArray;
            }
        }
        
        $result->duration_unit = Dbm_Share_Helper_Data::DURATION_MINUTES;
        $result->cooking_duration_unit = Dbm_Share_Helper_Data::DURATION_MINUTES;

        if($photoData)
        {
            $photo = new stdClass();
            $photo->data = base64_encode($photoData);
            $photo->filename = $_FILES['photo']['name'];
            $photo->gmaps_label = $params['gmaps_label'];
            $result->photos = array($photo);
        }
        
        return $result;
    }
    
    public function abuseElementAction()
    {
        $this->_abuse(Dbm_Share_Helper_Abuse::TYPE_ELEMENT);
    }
    
    public function abuseCommentAction()
    {
        $this->_abuse(Dbm_Share_Helper_Abuse::TYPE_COMMENT);
    }
    
    protected function _abuse($type)
    {
        $session = Mage::getSingleton('core/session');
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $id = $this->getRequest()->getParam('id', null);
        
        switch($type)
        {
            case Dbm_Share_Helper_Abuse::TYPE_COMMENT:
                $model = Mage::getModel('dbm_share/comment');
                break;
            case Dbm_Share_Helper_Abuse::TYPE_ELEMENT:
                $model = Mage::getModel('dbm_share/element');
                break;
        }
        
        if($model)
        {
            $model->load($id);

            if($customer->getId() && $model->getId())
            {
                if($model->abuse($model))
                {
                    $session->addSuccess($this->__('Element declared as an abuse'));
                }
                else
                {
                    $session->addError($this->__('You have already declared an abuse on this element'));
                }
            }
        }
        
        $mask = Mage::app()->getStore()->getBaseUrl();
        $url = $this->_getRefererUrl();
        $url = str_replace($mask, '', $url);

        $finalUrl = Mage::getUrl($url);
        $this->_redirectUrl($finalUrl);
    }
    
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $element = Mage::getModel('dbm_share/element');
        $element->load($id);
        $session = Mage::getSingleton('core/session');
        
        if($customer && $customer->getId() == $element->getIdCustomer() && $element->getId())
        {
            $session->addSuccess($this->__('Element deleted'));
            $element->delete();
        }
        else
        {
            $session->addError('Impossible to delete this element');
        }
        
        $this->_redirectUrl(Mage::getUrl('club'));
    }
}