<?php
class Netresearch_OPS_Test_Helper_PaymentTest extends EcomDev_PHPUnit_Test_Case
{
    private $_helper;
 
    public function setUp()
    {
        parent::setup();
        $this->_helper = Mage::helper('ops/payment');
    }
    
    public function testIsPaymentAuthorizeType()
    {
        $this->assertTrue($this->_helper->isPaymentAuthorizeType(Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED));
        $this->assertTrue($this->_helper->isPaymentAuthorizeType(Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING));
        $this->assertTrue($this->_helper->isPaymentAuthorizeType(Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN));
        $this->assertTrue($this->_helper->isPaymentAuthorizeType(Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT));
        $this->assertFalse($this->_helper->isPaymentAuthorizeType(0));
    }
    
    public function testIsPaymentCaptureType()
    {
        $this->assertTrue($this->_helper->isPaymentCaptureType(Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED));
        $this->assertTrue($this->_helper->isPaymentCaptureType(Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING));
        $this->assertTrue($this->_helper->isPaymentCaptureType(Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN));
        $this->assertFalse($this->_helper->isPaymentCaptureType(0));
    }
}

