<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Block_System_Config_Allowimport extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = $element->getElementHtml();
        
        $html .= "<script>
function changeAllowImportOption() {
    var allowImport = $('ordersync_settings_general_allowimport').value == 1;
    if (allowImport) {
        $('row_ordersync_settings_general_sendemail').show();
    }
    else {
        $('row_ordersync_settings_general_sendemail').hide();
    }
    return true;
}
Event.observe($('ordersync_settings_general_allowimport'), 'change', changeAllowImportOption);
setTimeout(changeAllowImportOption, 100);
</script>";
        
        return $html;
    }
}