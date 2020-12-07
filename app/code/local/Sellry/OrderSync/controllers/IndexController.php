<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.2
 */

class Sellry_OrderSync_IndexController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this
            ->loadLayout()
            ->_setActiveMenu('system')
            ->_addContent($this->getLayout()->createBlock('ordersync/grid'));

        return $this;
    }

    public function indexAction() {
        $this
            ->_initAction()
            ->renderLayout();
    }

    
    public function downloadAction() {
        
        $filename = $this->getRequest()->getParam('file', 'fake');
        $file = Mage::getBaseDir('var') . DS . 'log' . DS . $filename;
        if (file_exists($file)) {
            $this->_prepareDownloadResponse($filename, file_get_contents($file));
        }
    }
    
    public function forcesyncAction() {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $fromDate = $this->getRequest()->getParam('fd', null);
            $result = Mage::getModel("ordersync/observer")
                    ->setIsForce(true)
                    ->setExportFrom($fromDate)
                    ->syncIt();

            if ($result['success']) {
                $session->addSuccess($result['msg']);
            } else {
                $session->addError($result['msg']);
            }
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function forcestockAction() {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $result = Mage::getModel("ordersync/observer")->updateStock();
            if ($result['success']) {
                $session->addSuccess($result['msg']);
            } else {
                $session->addError($result['msg']);
            }
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function clearAction() {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $helper = Mage::helper('ordersync');
            $oldLogName = $helper->getLogLocation() . $helper->getLogFilename();
            $newLogName = $helper->getLogLocation() . "ordersync_" . gmdate('Y-m-d_H-i-s') . ".log";
            
            rename($oldLogName, $newLogName);
            $session->addSuccess('Log cleared.');
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
    
    
    public function clearhistoryAction() {
        $session = Mage::getSingleton('adminhtml/session');
        if($_GET['file'] != 'all'){
            @unlink($_SERVER['DOCUMENT_ROOT'].'/var/log/'.$_GET['file']);
            $str = 'File is deleted.';
        }
        else {
            if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/var/log/')) { 
            while (false !== ($file = readdir($handle))) { 
               if(preg_match('/ordersync_/u',$file))@unlink($_SERVER['DOCUMENT_ROOT'].'/var/log/'.$file); ; 
               } 
            } 
            $str = 'Files are deleted.'; 
        }
        
        
        $session->addSuccess($str);
        $this->_redirect('*/*/');

    }
}
