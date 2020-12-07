<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Block_System_Config_Allowstockupdate extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = $element->getElementHtml();
        
        $html .= "<script>
function changeAllowStockUpdateOption() {
    var allowUpdate = $('ordersync_settings_general_allowstockupdate').value == 1;
    if (allowUpdate) {
        $('row_ordersync_settings_general_updateskus').show();
        $('row_ordersync_settings_general_updatedisabled').show();
    }
    else {
        $('row_ordersync_settings_general_updateskus').hide();
        $('row_ordersync_settings_general_updatedisabled').hide();
    }
    return true;
}
Event.observe($('ordersync_settings_general_allowstockupdate'), 'change', changeAllowStockUpdateOption);
setTimeout(changeAllowStockUpdateOption, 100);
</script>";
        
        return $html;
    }
}