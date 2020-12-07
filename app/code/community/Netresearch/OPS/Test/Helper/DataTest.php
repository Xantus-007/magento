<?php
class Netresearch_OPS_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected $store;
 
    public function setUp()
    {
        parent::setup();
        $this->helper = Mage::helper('ops');
        $this->store  = Mage::app()->getStore(0)->load(0);
    }
    
    /**
     * @test
     */
    public function getModuleVersionString()
    {
        $path = 'modules/Netresearch_OPS/version';

        Mage::getConfig()->setNode('modules/Netresearch_OPS/version', '120301');
        $this->assertSame('OGmg120301', $this->helper->getModuleVersionString());

        Mage::getConfig()->setNode('modules/Netresearch_OPS/version', '120612');
        $this->assertSame('OGmg120612', $this->helper->getModuleVersionString());

        $this->store->resetConfig();
    }
}

