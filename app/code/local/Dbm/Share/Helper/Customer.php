<?php

class Dbm_Share_Helper_Customer extends Mage_Core_Helper_Abstract
{
    public function getAllowedPointsForElement(Mage_Customer_Model_Customer $customer, $element)
    {
        $result = 0;
        if($customer->getId() && $element->getId())
        {
            switch($element->getType())
            {
                case Dbm_Share_Model_Element::TYPE_PHOTO:
                    switch($customer->getProfileStatus())
                    {
                        case 1:
                        case 2:
                        case 3:
                            $result = 0.1;
                            break;
                    }
                    break;
                case Dbm_Share_Model_Element::TYPE_RECEIPE:

                    switch($customer->getProfileStatus())
                    {
                        case 1:
                        case 2:
                        case 3:
                            $result = 10;
                            break;
                    }
                    break;
            }
        }

        return $result;
    }
}