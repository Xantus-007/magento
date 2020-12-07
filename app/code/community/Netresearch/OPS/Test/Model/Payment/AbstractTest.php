<?php
class Netresearch_OPS_Test_Model_Payment_AbstractTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test 
     */
    public function _getOrderDescription()
    {
        $items = array(
            new Varien_Object(array(
                'parent_item' => false,
                'name'       => 'abc'
            )),
            new Varien_Object(array(
                'parent_item' => true,
                'name'       => 'def'
            )),
            new Varien_Object(array(
                'parent_item' => false,
                'name'       => 'ghi'
            )),
            new Varien_Object(array(
                'parent_item' => false,
                'name'       => 'Dubbelwerkende cilinder Boring ø70 Stang ø40 3/8'
            )),
            new Varien_Object(array(
                'parent_item' => false,
                'name'       => '0123456789012345678901234567890123456789012xxxxxx'
            )),
        );

        $order = $this->getModelMock('sales/order', array('getAllItems'));
        $order->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue($items));

        $result = Mage::getModel('ops/payment_abstract')->_getOrderDescription($order);
        $this->assertEquals(
            'abc, ghi, Dubbelwerkende cilinder Boring 70 Stang 40 38, 0123456789012345678901234567890123456789012',
            $result
        );
    }
}
