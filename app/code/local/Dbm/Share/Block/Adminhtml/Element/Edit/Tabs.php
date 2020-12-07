<?php

class Dbm_Share_Block_Adminhtml_Element_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('dbm_share_element_tabs');
        $this->setDestElementId('edit_form');
        //$this->setTitle('Modification d\'un élément de partage');
    }

    public function _beforeToHtml()
    {
        $shareHelper = Mage::helper('dbm_share');

        $this->addTab('general', array(
            'label' => 'Général',
            'content' => $this->getLayout()->createBlock('dbm_share/adminhtml_element_edit_tab_general')->toHtml()
        ));

        foreach($shareHelper->getAllowedLocales() as $locale)
        {
            $country = $shareHelper->getCountryFromLocale($locale);
            $tabs = $this->getLayout()->createBlock('dbm_share/adminhtml_element_edit_tab_content');
            $tabs->setLocale($locale);

            $this->addTab('content_'.$locale, array(
                'label' => 'Contenu '.strtoupper($country),
                //'content' => $this->getLayout()->createBlock('dbm_share/adminhtml_element_edit_tab_content_'.uc_words(str_replace('_', '', $locale)))->toHtml()
                'content' =>$tabs->toHtml()
            ));
        }

        return parent::_beforeToHtml();
    }

}