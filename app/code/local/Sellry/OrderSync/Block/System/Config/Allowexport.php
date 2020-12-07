<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Block_System_Config_Allowexport extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = $element->getElementHtml();
        
        $html .= "<script>
function changeAllowExportOption() {
    var allowExport = $('ordersync_settings_general_allowexport').value == 1;
    if (allowExport) {
        $('row_ordersync_settings_general_exportfrom').show();
        $('row_ordersync_settings_general_exportskus').show();
        $('row_ordersync_settings_general_exportdisabled').show();
    }
    else {
        $('row_ordersync_settings_general_exportfrom').hide();
        $('row_ordersync_settings_general_exportskus').hide();
        $('row_ordersync_settings_general_exportdisabled').hide();
    }
    return true;
}
Event.observe($('ordersync_settings_general_allowexport'), 'change', changeAllowExportOption);
setTimeout(changeAllowExportOption, 100);
</script>";
        
        return $html;
    }
}