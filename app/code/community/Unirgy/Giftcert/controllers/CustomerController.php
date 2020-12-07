<?php
/**
 * Unirgy_Giftcert extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_Giftcert
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_Giftcert
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Unirgy_Giftcert_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function balanceAction()
    {
        $handles = array('default');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $handles[] = 'customer_account';
        }
        $this->loadLayout($handles);
        $this->renderLayout();
    }

    public function printoutAction()
    {
        $req       = $this->getRequest();
        $productId = $req->getParam('product');
        $pdfId     = $req->getParam('pdf_template');
        try {
            if (!$pdfId) {
                if ($productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $pdfId   = $product->hasData('ugiftcert_pdf_tpl_id') ? $product->getData('ugiftcert_pdf_tpl_id') : Mage::getStoreConfig('ugiftcert/email/pdf_template');
                    if (!$pdfId) {
                        Mage::getSingleton('customer/session')->addNotice($this->__("No valid PDF ID found"));
                    }
                } else {
                    Mage::getSingleton('customer/session')->addNotice($this->__("No PDF or product ID found."));
                }
            }
            if ($pdfId) {
                $pdf      = Mage::getModel('ugiftcert/pdf_model')->load($pdfId);
                $store    = Mage::app()->getStore();
                $settings = $pdf->getData('settings');
                if (!is_array($settings)) {
                    $settings = Zend_Json::decode($settings);
                }
                $recipName = urldecode($req->getParam('recipient_name'));
                $recipEmail = urldecode($req->getParam('recipient_email'));
                $recipAddress = urldecode($req->getParam('recipient_address'));
                $msg = urldecode($req->getParam('recipient_message'));
                $senderName = urldecode($req->getParam('sender_name'));
                $amount = urldecode($req->getParam('amount'));
                $data = new Varien_Object(array(
                                               'store'       => $store,
                                               'email'       => $recipEmail,
                                               'name'        => $recipName,
                                               'sender_name' => $senderName,
                                               'gc'          => new Varien_Object(array(
                                                           'cert_id'           => '-1',
                                                           'cert_number'       => 'TEST-CODE',
                                                           'balance'           => $amount,
                                                           'pin'               => '0000',
                                                           'status'            => 'A',
                                                           'currency_code'     => $store->getCurrentCurrencyCode(),
                                                           'expire_at'         => date('Y-m-d H:s:i'),
                                                           'recipient_name'    => $recipName,
                                                           'recipient_email'   => $recipEmail,
                                                           'recipient_address' => $recipAddress,
                                                           'recipient_message' => $msg,
                                                           'store_id'          => $store->getId(),
                                                           'sender_name'       => $senderName,
                                                           'pdf_settings'      => $settings,
                                                      )),
                                          ));

                $printout = Mage::helper('ugiftcert')->outputPdfPrintout($data);
                $fileName = $pdfId . '_preview.pdf';

                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', 'application/pdf', true)
                    ->setHeader('Content-Length', strlen($printout))
                    ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                    ->setHeader('Last-Modified', date('r'))
                    ->setBody($printout);

                return;
            }
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addNotice($e->getMessage());
        }
        $this->_redirect('catalog/product/view', array('id' => $productId));
        return;
    }
}
