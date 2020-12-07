<?php

class Wizkunde_ConfigurableBundle_Model_Images
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getModel('configurablebundle/image')->getCollection();

        $returnData = array();
        foreach($collection as $item) {
            $returnData[] = array('value' => $item->getData('id'), 'label' => $item->getData('name'));
        }

        return $returnData;
    }
}