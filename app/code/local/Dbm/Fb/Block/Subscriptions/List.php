<?php

class Dbm_Fb_Block_Subscriptions_List extends Dbm_Customer_Block_Subscriptions_List
{
    protected function _getCollection()
    {
        $fb = Mage::getModel('dbm_fb/api');

        return $fb->findMyFriends();
    }
}