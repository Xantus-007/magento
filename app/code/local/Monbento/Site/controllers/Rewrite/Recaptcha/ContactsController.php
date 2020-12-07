<?php

require_once(Mage::getModuleDir('controllers', 'Fontis_Recaptcha') . DS . 'ContactsController.php');

class Monbento_Site_Rewrite_Recaptcha_ContactsController extends Fontis_Recaptcha_ContactsController
{

    public function postAction()
    {
        $refererUrl = '*/';
        if (!(Mage::getStoreConfig("fontis_recaptcha/recaptcha/when_loggedin") && (Mage::getSingleton('customer/session')->isLoggedIn()))) {
            if (Mage::getStoreConfig("fontis_recaptcha/recaptcha/contacts")) {
                $privatekey = Mage::getStoreConfig("fontis_recaptcha/setup/private_key");
                // check response
                $resp = Mage::helper("fontis_recaptcha")->recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]
                );
                if ($resp == true) { // if recaptcha response is correct, use core functionality
                    parent::postAction();
                } else { // if recaptcha response is incorrect, reload the page
                    Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Your reCAPTCHA entry is incorrect. Please try again.'));

                    $_SESSION['contact_comment'] = $_POST['comment'];
                    $_SESSION['contact_name'] = $_POST['name'];
                    $_SESSION['contact_email'] = $_POST['email'];
                    $_SESSION['contact_telephone'] = $_POST['telephone'];

                    $this->_redirect($refererUrl);
                    return;
                }
            } else { // if recaptcha is not enabled, use core function alone
                parent::postAction();
                $this->_redirect($refererUrl);
            }
        } else { // if recaptcha is not enabled, use core function alone
            parent::postAction();
            $this->_redirect($refererUrl);
        }
    }

}

?>
