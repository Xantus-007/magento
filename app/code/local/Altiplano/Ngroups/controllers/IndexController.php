<?php
class Altiplano_Ngroups_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    			
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()){

            $session   = Mage::getSingleton('core/session');
            
            $status = Mage::getModel('newsletter/subscriber')->subscribe(trim($data['mail']));
            if ($status > 0){
                $user = Mage::getModel('newsletter/subscriber')->loadByEmail(trim($data['mail']));
                $id = $user->getId();
                $user->confirm($user->getCode());
            }
            if ($data['sgroup'] != '0'){
                $model = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $data['sgroup'])->toArray();
            
                $addData['customers'] = $model['items'][0]['customers'].",".$id;

                $model = Mage::getModel('ngroups/ngroups');

                $model->setData($addData)
                        ->setId($data['sgroup']);
                try{
                    $model->save();
                }catch(Exception $e){}
            }
            $session->addSuccess($this->__('Thank you for your subscription'));
            
       }
       $this->_redirectReferer();
       
        
    }
}