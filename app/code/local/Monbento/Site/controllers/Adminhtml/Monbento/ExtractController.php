<?php

class Monbento_Site_Adminhtml_Monbento_ExtractController extends Mage_Adminhtml_Controller_Action
{
 
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function giftcertAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function postGiftcertAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                $this->_getSession()->addError($this->__('Invalid form data.'));
                $this->_redirectReferer();
            }
            else
            {
                ini_set('memory_limit', '3G');
                ini_set('max_execution_time', 0);
                $dateFormat = 'yyyy-MM-dd HH:mm:ss';
                $outputDateFormat = 'dd/MM/yyyy HH:MM:ss';
                $startDate = date('Y-m-d H:i:s',  strtotime($post['extract']['datefrom']));
                $endDate = date('Y-m-d H:i:s',  strtotime($post['extract']['dateto']));

                $headers = array(
                    'Carte cadeau',
                    'CMDE ACHAT CARTE',
                    'Montant carte cadeau',
                    'Balance carte cadeau',
                    'Date achat de la carte cadeau',
                    'CMDE 1',
                    'CMDE 2'
                );

                $io = new Varien_Io_File();
                $path = Mage::getBaseDir('var') . DS . 'export' . DS;
                $name = 'giftcerts';
                $file = $path . DS . $name . '.csv';
                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $path));
                $io->streamOpen($file, 'w+');
                $io->streamLock(true);

                $io->streamWriteCsv($headers);

                $lstOrderCert = Mage::getModel('ugiftcert/history')->getCollection()
                    ->addFieldToFilter('ts', array('from' => $startDate))
                    ->addFieldToFilter('ts', array('to' => $endDate))
                    ->addFieldToFilter('action_code', array('eq' => 'order'))
                    ->addFieldToFilter('status', array('eq' => 'A'))
                ;

                foreach($lstOrderCert as $cert)
                {
                    $giftcert = Mage::getModel('ugiftcert/cert')->load($cert->getCertId());
                    $orders = Mage::getModel('sales/order')->getCollection()
                            ->addFieldToFilter('giftcert_code', array('like' => '%'.$giftcert->getCertNumber().'%'));
                    $giftcertAmount = Mage::getModel('ugiftcert/history')->getCollection()
                            ->addFieldToFilter('action_code', array('eq' => 'create'))
                            ->addFieldToFilter('cert_id', array('eq' => $cert->getCertId()))
                            ->getFirstItem();

                    $ordersId = array();
                    foreach($orders as $order)
                    {
                        $ordersId[] = $order->getIncrementId();
                    }

                    $data = array(
                        'giftcert' => $giftcert->getCertNumber(),
                        'giftcert_order_id' => Mage::getModel('sales/order')->load($giftcertAmount->getOrderId())->getIncrementId(),
                        'giftcert_amount' => $giftcertAmount->getAmount(),
                        'giftcert_available' => $giftcert->getBalance(),
                        'date_giftcert' => $giftcertAmount->getTs(),
                        'order_id_1' => $ordersId[0],
                        'order_id_2' => $ordersId[1]
                    );

                    if(isset($ordersId[0])) $data['order_id_1'] = $ordersId[0];
                    if(isset($ordersId[1])) $data['order_id_1'] = $ordersId[1];

                    $io->streamWriteCsv($data);
                }

                $io->close();

                $content = array(
                    'type'  => 'filename',
                    'value' => Mage::getBaseDir('var') . DS . 'export' . DS . 'giftcerts.csv',
                    'rm'    => true
                );

                $this->_prepareDownloadResponse('giftcerts.csv', $content);
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/extract/giftcert');  
    }
}
