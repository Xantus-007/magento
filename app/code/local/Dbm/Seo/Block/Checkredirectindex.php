<?php

class Dbm_Seo_Block_Checkredirectindex extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('dbm-seo/index/redirectionindex'); //

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Tester maintenant !')
                    ->setOnClick("popWin('$url', 'Check Redirect index.php', 'width=320,height=180,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=no')")
                    ->toHtml();

        return $html;
    }

}
