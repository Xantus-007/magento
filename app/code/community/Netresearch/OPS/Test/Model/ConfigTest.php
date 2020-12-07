<?php
class Netresearch_OPS_Test_Model_ConfigTest extends EcomDev_PHPUnit_Test_Case
{
    private $_model;

    public function setUp()
    {
        parent::setup();
        $this->_model = Mage::getModel('ops/config');
    }

    public function testType()
    {
        $this->assertInstanceOf('Netresearch_OPS_Model_Config', $this->_model);
    }

    public function testGetIntersolveBrands()
    {
        $this->assertTrue(is_array($this->_model->getIntersolveBrands(null)));
        $this->assertEquals(0, sizeof($this->_model->getIntersolveBrands(null)));

        $path = 'payment/ops_interSolve/brands';

        $newVouchers = array(
                array('brand' => '1234', 'value' => '1234'),
                array('brand' => '5678', 'value' => '5678'),
                array('brand' => '9012', 'value' => '9012'),
        );

        $store = Mage::app()->getStore(0)->load(0);
        $store->setConfig($path, serialize($newVouchers));
        $this->assertEquals(sizeof($newVouchers), sizeof($this->_model->getIntersolveBrands(null)));
    }
    
}

