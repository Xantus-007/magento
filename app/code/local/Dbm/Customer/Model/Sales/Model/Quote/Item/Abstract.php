<?php

abstract class Dbm_Customer_Model_Sales_Quote_Item_Abstract extends Mage_Sales_Model_Quote_Item_Abstract
{
    public function getPrice() {
        return 10;
    }
}