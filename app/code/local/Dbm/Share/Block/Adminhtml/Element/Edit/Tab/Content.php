<?php

class Dbm_Share_Block_Adminhtml_Element_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $element = Mage::getModel('dbm_share/element');
        $params = $this->getRequest()->getParams();

        $locale = $this->getLocale();
        $form = new Varien_Data_Form();
        $shareHelper = Mage::helper('dbm_share');
        $country = strtoupper($shareHelper->getCountryFromLocale($locale));
        $lang = $shareHelper->getLangFromLocale($locale);
        $type = Mage::registry('dbm_share_current_type');

        $fieldset = $form->addFieldset('content_'.$locale, array(
            'legend' => 'Contenu '.$country
        ));

        $fieldset->addField('title_'.$locale, 'text', array(
            'label' => 'Titre '.$country,
            'name' => 'title_'.$locale,
            //'required' => true
        ));

        if($type == Dbm_Share_Model_Element::TYPE_RECEIPE)
        {

            $fieldset->addField('ingredients_legend_'.$locale, 'text', array(
                'label' => 'Légende '.$country,
                'name' => 'ingredients_legend_'.$locale
            ));

            $fieldset->addField('ingredients_content_'.$locale, 'textarea', array(
                'label' => 'Ingrédients '.$country,
                'name' => 'ingredients_content_'.$locale
            ));

            $fieldset->addField('description_'.$locale, 'textarea', array(
                'label' => 'Recette '.$country,
                'name' => 'description_'.$locale,
                //'required' => true
            ));
        }

        $this->setForm($form);

        if(isset($params['id']) && $element->load($params['id']))
        {
            $form->setValues($element);
        }

        return parent::_prepareForm();
    }
}