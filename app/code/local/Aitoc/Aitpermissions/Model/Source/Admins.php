<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Source/Admins.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('5c8d3ba095c49c682bc216449aaaf1a6'); ?><?php
class Aitoc_Aitpermissions_Model_Source_Admins extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_data = null;

    public function getAllOptions()
    {
        if(is_null($this->_data)) {
            $this->_data = array('' => '');
            $collection = Mage::getModel('admin/user')
                ->getCollection();
            foreach($collection as $admin) {
                $this->_data[$admin->getId()] = $admin->getUsername();
            }
        }
        return $this->_data;
    }

    public function toOptionArray()
    {
        $array = array(
            array('value' => 0, 'label'=>Mage::helper('aitpermissions')->__('')),
        );
        
        /*UPDATE `alfer_m17`.`catalog_eav_attribute` SET `is_visible` = '1' WHERE `catalog_eav_attribute`.`attribute_id` =962 LIMIT 1 ;
        UPDATE `alfer_m17`.`eav_attribute` SET `frontend_input` = 'select',
`frontend_label` = 'Product owner',
`source_model` = 'aitpermissions/source_admins' WHERE `eav_attribute`.`attribute_id` =962 LIMIT 1 ;*/

        $levels = $this->getAllOptions();

        foreach($levels as $key=>$value)
        {
            $array[] = array('value' => $key, 'label'=>Mage::helper('aitpermissions')->__(ucfirst($value)));
        }

        return $array;
    }
    
    public function getOptionArray()
    {
        /*$array = array(
            0 => Mage::helper('aitpermissions')->__('')
        );*/

        $levels = $this->getAllOptions();

        foreach($levels as $key=>$value)
        {
            $array[$key] = Mage::helper('aitpermissions')->__(ucfirst($value));
        }

        return $array;
    }
    
} } 