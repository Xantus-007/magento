<?php

include_once "Mage/Contacts/controllers/IndexController.php";

class Monbento_V2_IndexController extends Mage_Contacts_IndexController
{
    public function postAction()
    {
        parent::postAction();
        $url = parse_url($_SERVER['HTTP_REFERER']);
        $this->_redirect(ltrim($url['path'], '/'));
    }
}