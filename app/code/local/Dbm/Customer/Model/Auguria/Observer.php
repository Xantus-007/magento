<?php

class Dbm_Customer_Model_Auguria_Observer extends Auguria_Sponsorship_Model_Observer
{
    /**
     * Add fidelity points to customer
     * @param Mage_Customer_Model_Customer $customer
     * @param float $tFPoints
     * @param String $recordType
     * @param Int $recordId
     * @param Datetime $datetime
     */
    protected function _addFidelityPoints($customer, $fidelityPoints, $recordType='order', $recordId=0, $datetime=null)
    {
        if($recordType == 'order' || Mage::helper('dbm_share')->isTypeAllowed($recordType))
        {
            $hasProfile = Mage::helper('dbm_customer')->isValidProfile($customer);
            
            if(!$hasProfile)
            {
                $fidelityPoints = 0;
            }
            
            if($fidelityPoints > 0)
            {
                $customer = Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, $fidelityPoints);
                //enregistrement dans les logs
                $data = array(
                    'customer_id' => $customer->getId(),
                    'record_id' => $recordId,
                    'record_type' => $recordType,
                    'datetime' => $datetime,
                    'points' => $fidelityPoints
                );
                $this->_addAuguriaSponsorshipLog($data);
            }
        }
    }
    
    /*
     * Calcul total order points on sales_order_payment_pay event
     * @param Varien_Event_Observer $observer
     */
    public function calcPoints($observer)
    {
    	/**
    	 * @TODO : retrancher du total les points offerts
    	 */
    	try {
            //modules actifs
            $moduleFidelity = Mage::helper('auguria_sponsorship/config')->isFidelityEnabled();
            $moduleSponsor = Mage::helper('auguria_sponsorship/config')->isSponsorshipEnabled();
            $moduleAccumulated = Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled();

            if ($moduleFidelity==1||$moduleSponsor==1||$moduleAccumulated==1)
            {
                //récupération de la commande et des articles
                $order = $observer->getInvoice()->getOrder();
                $orderDate = $order->getUpdatedAt();
                $orderId = $order->getEntityId();

                //définition du client
                $cId = $order->getCustomerId();

                //definition du websiteid
                $wId = $order->getStore()->getWebsiteId();

                //definition du groupe du client
                $customer = Mage::getModel('customer/customer')->load($cId);
                $gId = $customer->getGroupId();

                //definition du sponsor de premier niveau
                $sponsorId = (int)$customer->getSponsor();
                $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                $special_rate = (int)$sponsor->getData('special_rate');

                //variable de points
                $tCatalogFidelityPoints=0;
                $tCatalogSponsorPoints=0;
                $tCartFidelityPoints=0;
                $tCartSponsorPoints=0;

                //calcul des points catalogue et mise à jour de lacommande pour chaque ligne
                foreach ($order->getAllItems() as $item)
                {
                    //Add points only if product have no parent
                    if (!$item->getParentItemId())
                    {
                        $date = $item->getData('updated_at');
                        $pId = $item->getData('product_id');
                        $qte = $item->getData('qty_ordered');
                        $data = $item->getData();

                        if ($moduleFidelity==1 || $moduleAccumulated==1)
                        {
                            //récupération et affectation des points catalog pour chaque article commandé
                            $catalogFidelityPoints = (float)$this->getRulePoints($date, $wId, $gId, $pId,'fidelity');
                            //multiplication des points par la quantité
                            $catalogFidelityPoints = $catalogFidelityPoints*$qte;
                            //ajout des points aux items de commande
                            $data['catalog_fidelity_points'] = $catalogFidelityPoints;

                            //calcul du total de points catalogue
                            $tCatalogFidelityPoints = $tCatalogFidelityPoints+$catalogFidelityPoints;

                            //calcul du total de points panier
                            $tCartFidelityPoints = $tCartFidelityPoints+(float)$item->getCartFidelityPoints();
                        }

                        if (($moduleAccumulated==1 || $moduleSponsor==1) && $special_rate==0)
                        {
                                //récupération et affectation des points catalog pour chaque article commandé
                            $catalogSponsorPoints = $this->getRulePoints($date, $wId, $gId, $pId, 'sponsor');

                            //multiplication des points par la quantité
                            $catalogSponsorPoints = $catalogSponsorPoints*$qte;

                            //ajout des points aux items de commande
                            $data['catalog_sponsor_points'] = $catalogSponsorPoints;
                            //calcul du total de points catalogue
                            $tCatalogSponsorPoints = $tCatalogSponsorPoints+$catalogSponsorPoints;

                            //calcul du total de points panier
                            $tCartSponsorPoints = $tCartSponsorPoints+$item->getCartSponsorPoints();
                        }
                            //si un taux spécial est défini pour le parrain direct
                        elseif (($moduleAccumulated==1 || $moduleSponsor==1) && $special_rate!=0)
                        {
                            //Redéfinition du taux à appliquer dans la commande						
                            //ajout des points aux items de commande
                            $data['catalog_sponsor_points'] = 0;
                            $specialratepoints = $item->getData('price')*$qte*$special_rate/100;
                            $data['cart_sponsor_points'] = $specialratepoints;
                        }

                        $item->setData($data);
                        $item->save();
                    }
                }
                $dbmPoints = intval($order->getGrandTotal());
                $order->save();

                //Ajout du total des points fidelite
                if($dbmPoints > 0)
                {
                    $this->_addFidelityPoints($customer, $dbmPoints, 'order', $orderId, $orderDate);
                    $customer->save();
                }

                //Ajout du total des points de parrainage si le parrain n'a pas de taux spécial
                if (($tCatalogSponsorPoints != 0 || $tCartSponsorPoints != 0) && $special_rate==0) 
                {
                    $this->_addSponsorPoints($sponsor, $customer, $tCatalogSponsorPoints+$tCartSponsorPoints, 'order', $orderId, $orderDate);
                }
                //Ajout des points à partir du taux spécial au parrain direct uniquement
                elseif ($special_rate!=null && ($moduleSponsor==1 || $moduleAccumulated==1)) 
                {
                    $this->_addSponsorSpecialPoints($sponsor, $customer, $specialratepoints, 'order', $orderId, $orderDate);
                }
            }
            return $this;
    	}
    	catch (Exception $e) {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while saving points : %s",$e->getMessage()));
    	}
    }
    
    /**
     * 
     * Add fidelity points to customer and sponsorship points to sponsor on first order
     * @param Varien_Event_Observer $observer
     */
    public function addFirstOrderPoints($observer)
    {
    	try
    	{
            $godsonFirstOrderPoints = Mage::helper('auguria_sponsorship/config')->getFidelityFirstOrderPoints();
            $sponsorFirstOrderPoints =  Mage::helper('auguria_sponsorship/config')->getGodsonFirstOrderPoints();

            //Check if we have to add points on first order
            if ($godsonFirstOrderPoints>0 || $sponsorFirstOrderPoints>0)
            {
                $invoice = $observer->getInvoice();
                $customerId = $invoice->getCustomerId();

                //Get customer paid invoices
                $invoices = Mage::getResourceModel('sales/order_invoice_collection');
                $invoices->getSelect()->join(array('o'=>$invoices->getTable('sales/order')), 'main_table.order_id = o.entity_id','o.customer_id');

                $invoices->addAttributeToSelect('state')
                    ->addAttributeToFilter('o.customer_id', $customerId)
                    ->addAttributeToFilter('main_table.state', Mage_Sales_Model_Order_Invoice::STATE_PAID);

                if ($invoices->count() == 0)
                {	
                    $customer = Mage::getModel('customer/customer')->load($customerId);

                    //Add fidelity points to customer
                    if ($godsonFirstOrderPoints >0)
                    {
                        $this->_addFidelityPoints($customer, $godsonFirstOrderPoints, 'first', $invoice->getOrderId());
                        $customer->save();
                    }

                    //Add sponsorship points
                    $sponsorId = $customer->getSponsor();
                    if (isset($sponsorId) && $sponsorId>0 && $sponsorFirstOrderPoints>0)
                    {
                        $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                        
                        $cPoints = $sponsor->getData('points_other');
                        $sponsor->setData('points_other', Dbm_Customer_Helper_Data::POINTS_FIRST_ORDER + $cPoints);
                        $sponsor->save();
                        
                        Mage::helper('dbm_customer')->updateCustomerStatus($sponsor);
                        
                        $this->_addSponsorPoints($sponsor, $customer, $sponsorFirstOrderPoints, 'first', $invoice->getOrderId());
                    }
                    
                    //Add points to customer (10)
                    if(isset($sponsorId) && $sponsorId > 0)
                    {
                        Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, Dbm_Customer_Helper_Data::POINTS_FIRST_ORDER);
                        //$customer->setData('points_accumulated', Dbm_Customer_Helper_Data::POINTS_FIRST_ORDER + $points);
                        //$customer->save();
                        Mage::helper('dbm_customer')->addCustomerPoints($customer, Dbm_Customer_Helper_Data::ATTRIBUTE_POINTS_OTHER, Dbm_Customer_Helper_Data::POINTS_FIRST_ORDER);
                    }
                }
            }
    	}
    	catch (Exception $e)
    	{
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while adding first order points :".$e->getMessage()));
    	}
    }
}
