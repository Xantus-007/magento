<?php

class Altiplano_NoRegionNbOrder_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{

    protected function _prepareLayout()
    {
        /*
        $this->getLayout()
             ->getBlock('head')
             ->addJs('monbento-noregion.js');
        */
        parent::_prepareLayout();
    }

}