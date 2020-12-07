<?php

class Dbm_Fb_TestController extends Mage_Core_Controller_Front_Action
{
    public function findAction()
    {
        $fb = Mage::getModel('dbm_fb/api');

        $friends = $fb->findMyFriends();

        print_r($friends);

        exit();

    }
}