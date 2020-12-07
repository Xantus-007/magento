<?php

class Dbm_Account_Block_Rewrite_Sales_Order_History extends Mage_Sales_Block_Order_History
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/history-dbm.phtml');
    }

}
