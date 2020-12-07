<?php
class Netresearch_OPS_Test_Block_FormTest extends EcomDev_PHPUnit_Test_Case
{
    private $_block;

    public function setUp()
    {
        parent::setup();
        $this->_block = Mage::app()->getLayout()->getBlockSingleton('ops/form');
    }

    public function testGetCcBrands()
    {
        $this->assertInternalType('array', $this->_block->getCcBrands());
    }

    public function testGetDirectDebitCountryIds()
    {
        $this->assertInternalType('array', $this->_block->getDirectDebitCountryIds());
    }
    
}
