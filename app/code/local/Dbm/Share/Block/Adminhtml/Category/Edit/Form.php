<?php

class Dbm_Share_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    function _prepareForm()
    {
        $catModel = Mage::getModel('dbm_share/category');
        $params = $this->getRequest()->getParams();
        $shareHelper = Mage::helper('dbm_share');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldSet('misc', array(
            'legend' => 'Intitulés de la catégorie'
        ));

        $fieldset->addField('id', 'hidden', array(
            'name' => 'id'
        ));

        $fieldset->addField('image', 'image', array(
            'label' => 'Pictogramme',
            'name' => 'image',
        ));
        
        $fieldset->addField('image2', 'image', array(
            'label' => 'Pictogramme #2',
            'name' => 'image2',
        ));
        
        $fieldset->addField('position', 'text', array(
            'label' => 'Position',
            'name' => 'position'
        ));

        foreach(Mage::helper('dbm_share')->getAllowedLocales() as $locale)
        {
            if(!in_array($locale, array('en_ie'))) {
                $lang = $shareHelper->getLangFromLocale($locale);
                $country = $shareHelper->getCountryFromLocale($locale);

                $fieldset->addField('title_'.$locale, 'text', array(
                    'label' => 'Titre '.  strtoupper($country),
                    'name' => 'title_'.$locale
                ));
            }
        }

        foreach(Mage::helper('dbm_share')->getAllowedLocales() as $locale)
        {
            if(!in_array($locale, array('en_ie', 'pt_pt', 'ja_jp'))) {
                $lang = $shareHelper->getLangFromLocale($locale);
                $country = $shareHelper->getCountryFromLocale($locale);

                $fieldset->addField('meta_description_'.$locale, 'text', array(
                    'label' => 'Meta description '.  strtoupper($country),
                    'name' => 'meta_description_'.$locale
                ));
            }
        }

        if(isset($params['id']) && $catModel->load($params['id']))
        {
            $form->setValues($catModel);

            $imagePath = $shareHelper->getCategoryImagePath(false, '/');//.$catModel->getImage();
            if($catModel->getImage())
            {
                $form->getElement('image')->setValue($imagePath.$catModel->getImage());
            }
            
            if($catModel->getImage2())
            {
                $form->getElement('image2')->setValue($imagePath.$catModel->getImage2());
            }
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}