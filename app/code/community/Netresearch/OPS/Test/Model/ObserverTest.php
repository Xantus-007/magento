<?php
class Netresearch_OPS_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    private $_model;
 
    public function setUp()
    {
        parent::setup();
        $this->_model = Mage::getModel('ops/observer');
    }
    
    public function testType()
    {
        $this->assertInstanceOf('Netresearch_OPS_Model_Observer', $this->_model);
    }

    public function testIsCheckoutWithCcOrDd()
    {
        if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
            $class = new ReflectionClass('Netresearch_OPS_Model_Observer');
            $method = $class->getMethod('isCheckoutWithCcOrDd');
            $method->setAccessible(true);
         
            $this->assertTrue($method->invokeArgs($this->_model,array('ops_cc')));
            $this->assertTrue($method->invokeArgs($this->_model,array('ops_directDebit')));
            $this->assertFalse($method->invokeArgs($this->_model,array('checkmo')));
        }
    }
}

