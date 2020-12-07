<?php

require_once('abstract.php');

class Dbm_Fidelity extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        $startDate = '2017-02-21 00:00:00';
        $endDate = '2017-03-03 23:59:00';
        
        $customers = Mage::getModel('customer/customer')->getCollection();
        $customers->getSelect()->join(array('order'=>$customers->getTable('sales/order')), 'e.entity_id = order.customer_id AND order.state IN ("processing","complete")',array('order_id' => 'order.entity_id', 'order_state' => 'order.state'));
        $customers->addFieldToFilter('created_at', array('from' => $startDate))
            ->addFieldToFilter('created_at', array('to' => $endDate))
            ->addAttributeToSelect('points_other')
            ->addAttributeToSelect('profile_nickname', 'left')
            ->addAttributeToFilter(array(
                array('attribute' => 'profile_nickname', 'null' => true),
                array('attribute' => 'profile_nickname', 'eq' => '')
            ));

        $customers->getSelect()->group('e.entity_id');

        foreach($customers as $customer)
        {
            $fidelityPoints = $customer->getPointsOther();
            echo 'CUSTOMER '.$customer->getId(). ' - Points to add '.$fidelityPoints."\n";
            
            $customer = Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, $fidelityPoints);
            $customer->save();
            //enregistrement dans les logs
            $datetime = Mage::getModel('core/date')->gmtDate();
            $data = array(
                'customer_id' => $customer->getId(),
                'record_id' => 0,
                'record_type' => 'admin',
                'datetime' => $datetime,
                'points' => $fidelityPoints
            );
            $log = Mage::getModel('auguria_sponsorship/log');
            $log->setData($data);
            $log->save();
        }

        exit;
    }
}

$shell = new Dbm_Fidelity();
$shell->run();
