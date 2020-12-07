<?php

class Dbm_Expedition_Model_Observer
{
    
    public function insertShippingUserInfoBlock(Varien_Event_Observer $observer)
    {
        // >> check for adminhtml/sales_order/view route here <<

        $giftOptionsBlock = $observer->getBlock();
        if ($giftOptionsBlock->getNameInLayout() !== 'gift_options') {
            // not interested in other blocks than gift_options
            return;
        }

        $customInfoBlock = Mage::app()->getLayout()->createBlock(
            'adminhtml/template',
            'shipping_user_info',
            array(
                'template' => 'sales/order/view/shipping_user_info.phtml',
                'order' => Mage::registry('current_order'),
            )
        );

        //die('ok');

        $giftOptionsHtml = $observer->getTransport()->getHtml();
        $customHtml  = $customInfoBlock->toHtml();

        $observer->getTransport()->setHtml($customHtml . $giftOptionsHtml);
    }

    public function salesSaveShipmentBefore(Varien_Event_Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        if (!empty($order)) {
            $session = Mage::getSingleton('admin/session');
            $sender_admin_id = $session->getUser()->getUserId();

            if($sender_admin_id){
                $order->setSenderAdminId($sender_admin_id)->save();
            }
        }

        return $this;
    }

}