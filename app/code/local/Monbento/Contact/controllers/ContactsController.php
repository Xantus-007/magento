<?php

require_once Mage::getModuleDir('controllers', "Fontis_Recaptcha") . DS . "ContactsController.php";

class Monbento_Contact_ContactsController
    extends Fontis_Recaptcha_ContactsController
{
    /**
     * Post action
     *
     * @return null|void
     */
    public function postAction()
    {
        if (!(Mage::getStoreConfig("fontis_recaptcha/recaptcha/when_loggedin") && (Mage::getSingleton('customer/session')->isLoggedIn()))) {
            if (Mage::getStoreConfig("fontis_recaptcha/recaptcha/contacts")) {
                $privatekey = Mage::getStoreConfig("fontis_recaptcha/setup/private_key");
                // check response
                $resp = Mage::helper("fontis_recaptcha")->recaptcha_check_answer(
                    $privatekey,
                    $_SERVER["REMOTE_ADDR"],
                    $_POST["recaptcha_challenge_field"],
                    $_POST["recaptcha_response_field"]
                );
                if ($resp == true) { // if recaptcha response is correct, use core functionality
                    $this->_postAction();
                } else { // if recaptcha response is incorrect, reload the page
                    Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Your reCAPTCHA entry is incorrect. Please try again.'));

                    $_SESSION['contact_comment'] = $_POST['comment'];
                    $_SESSION['contact_name'] = $_POST['name'];
                    $_SESSION['contact_email'] = $_POST['email'];
                    $_SESSION['contact_telephone'] = $_POST['telephone'];

                    $this->_redirect('contacts/');
                    return;
                }
            } else {
                // if recaptcha is not enabled, use core function alone
                $this->_postAction();
                $this->_redirect('contacts/');
            }
        } else {
            // if recaptcha is not enabled, use core function alone
            $this->_postAction();
            $this->_redirect('contacts/');
        }
    }

    /**
     * Post action
     *
     * @return null|void
     * @throws Exception
     */
    private function _postAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* Files traitement */
                $path = Mage::getBaseDir('media') . DS . 'monbento' . DS . 'contacts' . DS;
                mkdir($path, 0775, true);

                $photofilename = '';
                $invoicefilename = '';
                if (isset($_FILES)) {
                    if ($_FILES['photo']['name']) {
                        $uploader = new Varien_File_Uploader('photo');
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $destFile = $path . $_FILES['photo']['name'];
                        $filename = $uploader->getNewFileName($destFile);
                        if ($uploader->save($path, $filename)) {
                            $photofilename = $path . $filename;
                        } else
                            $error = true;
                    }
                    if ($_FILES['invoice']['name']) {
                        $uploader = new Varien_File_Uploader('invoice');
                        $uploader->setAllowedExtensions(array('pdf', 'jpg', 'jpeg', 'png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $destFile = $path . $_FILES['invoice']['name'];
                        $filename = $uploader->getNewFileName($destFile);
                        if ($uploader->save($path, $filename)) {
                            $invoicefilename = $path . $filename;
                        } else
                            $error = true;
                    }
                    //Sending files as attachment
                    if ($photofilename && file_exists($photofilename)) {
                        $fileContents = file_get_contents($photofilename);
                        $mailTemplate->getMail()->createAttachment(
                            $fileContents,
                            Zend_Mime::TYPE_OCTETSTREAM,
                            Zend_Mime::DISPOSITION_ATTACHMENT,
                            Zend_Mime::ENCODING_BASE64,
                            end(explode('/', $photofilename))
                        );
                    }
                    if ($invoicefilename && file_exists($invoicefilename)) {
                        $fileContents = file_get_contents($invoicefilename);
                        $mailTemplate->getMail()->createAttachment(
                            $fileContents,
                            Zend_Mime::TYPE_OCTETSTREAM,
                            Zend_Mime::DISPOSITION_ATTACHMENT,
                            Zend_Mime::ENCODING_BASE64,
                            end(explode('/', $invoicefilename))
                        );
                    }
                }

                if ($error) {
                    throw new Exception();
                }

                //Get subject info by key
                $info = Mage::helper('monbentocontact')->getSubjectByKey($postObject->subject);
                //Alter default recipient email
                $emailRecipient = isset($info['email']) ? $info['email'] : Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                $emailRecipient = explode(';', $emailRecipient);
                //Alter subject field
                $postObject->subject = isset($info['subject']) ? $info['subject'] : $postObject->subject;
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $emailRecipient,
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                //Delete file
                if ($photofilename)
                    unlink($photofilename);
                if ($invoicefilename)
                    unlink($invoicefilename);
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $this->_redirect('*/*/');
        }
    }

}
