<?php
require_once 'Mage/Adminhtml/controllers/Newsletter/QueueController.php';
class Altiplano_Ngroups_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Newsletter_QueueController
{
    public function saveAction()
    {
        try {
	        $queue = Mage::getModel('newsletter/queue');
	        // create new queue from template, if specified
			$templateId = $this->getRequest()->getParam('template_id');
            if ($templateId) {
                /* @var $template Mage_Newsletter_Model_Template */
                $template = Mage::getModel('newsletter/template')->load($templateId);

                if (!$template->getId() || $template->getIsSystem()) {
                    Mage::throwException($this->__('Wrong newsletter template.'));
                }

                $queue->setTemplateId($template->getId())
                    ->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);
            } else {
                $queue->load($this->getRequest()->getParam('id'));
            }

            if (!in_array($queue->getQueueStatus(),
                   array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
                         Mage_Newsletter_Model_Queue::STATUS_PAUSE))
            ) {
                $this->_redirect('*/*');
                return;
            }

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }

            $queue->setStores($this->getRequest()->getParam('stores', array()))
                ->setNewsletterSubject($this->getRequest()->getParam('subject'))
                ->setNewsletterSenderName($this->getRequest()->getParam('sender_name'))
                ->setNewsletterSenderEmail($this->getRequest()->getParam('sender_email'))
                ->setNewsletterText($this->getRequest()->getParam('text'))
                ->setNewsletterStyles($this->getRequest()->getParam('styles'));

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE
                && $this->getRequest()->getParam('_resume', false)) {
                $queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
            }


            $queue->save();
/*             exit; */
            Mage::getResourceModel('ngroups/queue')->addSubscribersToQueue($queue, array());
            $this->_redirect('*/*');
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            }
            else {
                $this->_redirectReferer();
            }
        }
    }

//    public function saveAction()
//    {
//    	$queue = Mage::getSingleton('newsletter/queue')
//    		->load($this->getRequest()->getParam('id'));
//
//    	if (!in_array($queue->getQueueStatus(),
//    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
//    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
//   			$this->_redirect('*/*');
//    		return;
//    	}
//
//    	$format = Mage::app()->getLocale()->getDateTimeFormat(
//            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
//        );
//
//    	if ($queue->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
//    	    if ($this->getRequest()->getParam('start_at')) {
//    	        $date = Mage::app()->getLocale()->date($this->getRequest()->getParam('start_at'), $format);
//    	        $time = $date->getTimestamp();
//	    		$queue->setQueueStartAt(
//	    			Mage::getModel('core/date')->gmtDate(null, $time)
//	    		);
//	    	} else {
//	    		$queue->setQueueStartAt(null);
//	    	}
//    	}
//    	$queue->setStores($this->getRequest()->getParam('stores', array()));
//
//    	$queue->addTemplateData($queue);
//
//        $queue->getTemplate()
//    		->setTemplateSubject($this->getRequest()->getParam('subject'))
//		->setUserGroup($this->getRequest()->getParam('user_group'))
//    		->setTemplateSenderName($this->getRequest()->getParam('sender_name'))
//    		->setTemplateSenderEmail($this->getRequest()->getParam('sender_email'))
//    		->setTemplateTextPreprocessed($this->getRequest()->getParam('text'));
//
//    	if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE
//    		&& $this->getRequest()->getParam('_resume', false)) {
//    		$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
//    	}
//
//
//
//    	$queue->setSaveTemplateFlag(true);
//
//    	try {
//    		$queue->save();
//    	}
//    	catch (Exception $e) {
//    		echo $e->getMessage();
//            exit;
//    	}
//	Mage::getResourceModel('ngroups/queue')->addSubscribersToQueue($queue, array());
//
//    	$this->_redirect('*/*');
//    }

}
