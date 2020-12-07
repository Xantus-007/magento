<?php

class Altiplano_NoRegionNbOrder_Block_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit
{

    protected function _prepareLayout()
    {
        $this->getLayout()
             ->getBlock('head')
             ->addJs('monbento-noregion.js');

        parent::_prepareLayout();
    }

}