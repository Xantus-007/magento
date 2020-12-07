<?php

require_once Mage::getModuleDir('controllers', 'Auguria_Sponsorship').DS.'PointsController.php';

class Dbm_Customer_Auguria_PointsController extends Auguria_Sponsorship_PointsController
{
    
    public function preDispatch() {
        parent::preDispatch();
        
        $finalPoints = 0;
        
        $customer = $this->_getCustomer();
        if($customer->getId())
        {
            $points = $this->getRequest()->getParam('coupon_points');

            if($points >= 200)// && $points < 5000)
            {
                $finalPoints = 200;
            }
            /*
            elseif($points >= 5000 && $points < 7500)
            {
                $finalPoints = 5000;
            }
            elseif($points >= 7500)
            {
                $finalPoints = 7500;
            }*/
        }
        
        $this->getRequest()->setPost('points', $finalPoints);
    }
    
    public function _redirect($path, $arguments=array())
    {

        if($this->getRequest()->getParam('club'))
        {
            $arguments['club'] = 1;
        }
        return parent::_redirect($path, $arguments);
    }

    public function loadLayout($handles=null, $generateBlocks=true, $generateXml=true)
    {
        if($this->getRequest()->getParam('club'))
        {
            return parent::loadLayout(array(
                'default',
                'share_default',
                'dbm_share_public_index_pepites_override'
            ));
        }
        else
        {
            return parent::loadLayout($handles, $generateBlocks, $generateXml);
        }
    }

    public function addActionLayoutHandles()
    {
        $update = $this->getLayout()->getUpdate();

        // load store handle
        $update->addHandle('STORE_'.Mage::app()->getStore()->getCode());

        // load theme handle
        $package = Mage::getSingleton('core/design_package');
        $update->addHandle('THEME_'.$package->getArea().'_'.$package->getPackageName().'_'.$package->getTheme('layout'));

        $actionHandle = strtolower(str_replace('dbm_customer_public_auguria_Points_', 'auguria_sponsorship_points_', $this->getFullActionName()));
        // load action handle
        if($this->getRequest()->getParam('club'))
        {
            $update->addHandle($actionHandle)->addHandle('dbm_share_public_index_pepites_override');
        }
        else
        {
            $actionHandle = strtolower(str_replace('dbm_customer_public_auguria_Points_', 'auguria_sponsorship_points_', $this->getFullActionName()));
            $update->addHandle($actionHandle);
            
        }
        

        return $this;
    }

    public function changeAction()
    {
        $module = $this->getRequest()->getParam('module');
        $type = $this->getRequest()->getParam('type');
        $resultValidate = $this->_validateChange($module, $type);
        if ($resultValidate == 'valide' )
        {
            $this->loadLayout();
            
            $this->getLayout()
            ->getBlock('customer');
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        }
        elseif ($resultValidate == 'inactif' )
        {
            $this->_redirect('*/*/');
        }
        elseif ($resultValidate == 'account' )
        {
            $this->_redirectUrl(Mage::helper('customer')->getAccountUrl().'edit');
        }
    }


    protected function _getValue($points, $type, $module)
    {
        $result = 0;
        $customerId = Mage::getSingleton('customer/session')->getId();
        $customer = Mage::getModel("customer/customer")->load($customerId);
        
        if($customer->getId())
        {
            $points = $this->getRequest()->getParam('coupon_points');
            $cPoints = $customer->getAccumulatedPoints();
            
            if($cPoints >= $points)
            {
                
                if($points >= 200)// && $points < 5000)
                {
                    $result = 8;
                }
                /*
                elseif($points >= 5000 && $points < 7500)
                {
                    $result = 12;
                }
                elseif($points >= 7500)
                {
                    $result = 16;
                }
                 */
            }
        }
        
        return $result;
    }
    
    protected function _getCustomer()
    {
        $customerId = Mage::getSingleton('customer/session')->getId();
        return Mage::getModel("customer/customer")->load($customerId);
    }
}