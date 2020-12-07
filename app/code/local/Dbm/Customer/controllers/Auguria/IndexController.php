<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once Mage::getModuleDir('controllers', 'Auguria_Sponsorship').DS.'IndexController.php';

class Dbm_Customer_Auguria_IndexController extends Auguria_Sponsorship_IndexController
{
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
}
