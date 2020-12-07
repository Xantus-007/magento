<?php

class Dbm_Customer_Model_Condition_Status extends Mage_Rule_Model_Condition_Abstract 
{

    /**
     * @TODO for whatever this it, check it and afterwards document it!
     *
     * @return Hackathon_DiscountForATweet_Model_Condition_Tweet
     */
    public function loadAttributeOptions() {
        $attributes = array(
            'customer_status' => 'Customer Status'
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @TODO for whatever this it, check it and afterwards document it!
     *
     * @return mixed
     */
    public function getAttributeElement() {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * @TODO for whatever this it, check it and afterwards document it!
     *
     * @return string
     */
    public function getInputType() {

        switch ($this->getAttribute()) {
            case 'customer_status':
                return 'select';
        }
        
        return 'string';
    }

    /**
     * @TODO for whatever this it, check it and afterwards document it!
     * @return string
     */
    public function getValueElementType() {
        return 'select';
    }

    public function getValueSelectOptions() {
        switch($this->getAttribute())
        {
            case 'customer_status':
                $options = Mage::helper('dbm_customer')->getProfileStatusForSelect();
                break;
        }
        
        return $options;
    }
    
    /**
     * Validate customer status
     * 
     * @param Varien_Object $object
     * @return boolean
     */
    public function validate(Varien_Object $object) 
    {
        $rule = $this->getRule();
        $result = false;
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        
        if($customer->getId())
        {
            $conditions = unserialize($rule->getConditionsSerialized());
            
            if($conditions)
            {
                foreach($conditions['conditions'] as $condition)
                {
                    $statusTest = false;
                    //$test = eval($condition->getValue().$conditions[].$customer->getProfileStatus());
                    $string = '$statusTest = ('.intval($condition['value']).$condition['operator'].intval($customer->getProfileStatus()).');';
                    eval($string);

                    if($condition['attribute'] == 'customer_status' && $statusTest)
                    {
                        $result = true;
                    }
                }
            }
        }
        
        /*
        $messages = $session->getTwitterMessages();
        if (is_array($messages)) {
            foreach ($messages as $m) {
                if ($this->validateAttribute($m)) {
                    return true;
                }
            }
        }
        */
        
        return $result;
    }
}