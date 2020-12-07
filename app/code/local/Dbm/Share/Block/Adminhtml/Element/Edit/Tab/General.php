<?php


class Dbm_Share_Block_Adminhtml_Element_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $element = Mage::getModel('dbm_share/element');
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('dbm_share');
        $currentType = Mage::registry('dbm_share_current_type');


        if(isset($params['id']))
        {
            $element->load($params['id']);
        }

        $catCollection = Mage::getModel('dbm_share/category')->getCollection();

        $form = new Varien_Data_Form();

        $infoSet = $form->addFieldset('readonly_infos', array(
            'legend' => 'Informations'
        ));

        $infoSet->addField('id', 'hidden', array(
            'name' => 'id',
        ));

        $infoSet->addField('type_label', 'note', array(
            'label' => 'Type',
            'text' => __(Mage::registry('dbm_share_current_type'))
        ));
        
        $infoSet->addField('show_in_home', 'select', array(
            'label' => 'Afficher sur l\'accueil',
            'name' => 'show_in_home',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $infoSet->addField('likes', 'note', array(
            'label' => 'Nombre de "likes"',
            'text' => '0'
        ));

        if($element->getId() > 0 && $element->getIdCustomer() > 0)
        {
            $customer = Mage::getModel('customer/customer')->load($element->getIdCustomer());

            $infoSet->addField('customer_info', 'link', array(
                'label' => 'Compte client associé',
                //'value' => $customer->getFirstname().' '.$customer->getLastname(),
                //'href' => $this->getUrl('customer/edit/', array('id' => $customer->getId()))
            ));
        }

        $infoSet->addField('id_customer', 'text', array(
            'label' => 'Id du compte client',
            'name' => 'id_customer',
            'required' => true
        ));

        if($currentType == Dbm_Share_Model_Element::TYPE_RECEIPE)
        {
            $infoSet->addField('price', 'select', array(
                'label' => 'Prix',
                'name' => 'price',
                'values' => $helper->getAllowedPriceValues()
            ));

            $infoSet->addField('level', 'select', array(
                'label' => 'Difficulté',
                'name' => 'level',
                'values' => $helper->getAllowedLevelValues()
            ));

            $durationFieldSet = $form->addFieldset('duration_fieldset', array(
                'legend' => 'Durées'
            ));

            $durationFieldSet->addField('duration', 'text', array(
                'label' => 'Durée de la recette',
                'name' => 'duration'
            ));

            $durationFieldSet->addField('duration_unit', 'select', array(
                'label' => 'Unitée de la durée de la recette',
                'name' => 'duration_unit',
                'values' => $helper->getTimeUnitsForSelect(),
            ));

            $durationFieldSet->addField('cooking_duration', 'text', array(
                'label' => 'Durée de cuisson',
                'name' => 'cooking_duration'
            ));

            $durationFieldSet->addField('cooking_duration_unit', 'select', array(
                'label' => 'Unité de la durée de cuisson',
                'name' => 'cooking_duration_unit',
                'values' => $helper->getTimeUnitsForSelect(),
            ));
        }

        $editSet = $form->addFieldset('rw_infos', array(
            'legend' => 'Autres informations'
        ));

        $infoSet->addField('type', 'hidden', array(
            'name' => 'type',
            'value' => Mage::registry('dbm_share_current_type')
        ));

        $editSet->addField('categories', 'multiselect', array(
            'label' => 'Catégories',
            'name' => 'categories',
            'values' => $catCollection->toAdminSelectArray()
        ));

        $editSet->addField('photo', 'image', array(
            'label' => 'Photo (Taille max.: '.$helper->getUploadMaxFilesize().')',
            'name' => 'photo'
        ));

        if(isset($params['id']))
        {
            $form->setValues($element);

            //Setting customer info :
            $form->getElement('customer_info')
                ->setValue($customer->getFirstname().' '.$customer->getLastname())
                ->setHref($this->getUrl('customer/edit/', array('id' => $customer->getId())))
            ;

            $prettyType = $helper->getPrettyType($element->getType());
            $form->getElement('type_label')->setText(ucfirst($prettyType));

            //Getting categories
            $categories = $element->getCategories()->getIds();
            $form->getElement('categories')->setValue($categories);

            $photoCollection = Mage::getModel('dbm_share/photo')->getCollection()->addElementFilter($element);
            if(count($photoCollection) > 0)
            {
                $photo = $photoCollection->getFirstItem();
                $photoName = $photo->getFilename();
                $photoPath = Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.'/'.Dbm_Share_Model_Photo::MEDIA_FOLDER . '/' . $helper->getPhotoDir($photoName, false, '/');
                $form->getElement('photo')->setValue($photoPath . $photoName );

                if($photo->getLat() && $photo->getLng())
                {
                    $geoData = $helper->getGMapsData($photo->getLat(), $photo->getLng());
                    if($geoData)
                    {
                        $infoSet->addField('geoloc', 'link', array(
                            'label' => 'Informations de géolocalisation',
                            'href' => 'https://maps.google.com/maps?q='.$photo->getLat().','.$photo->getLng().'&hl=fr&z=17',
                            'value' => $geoData['formatted_address']
                        ));
                    }
                }
            }
        }

        $form->setUseContainer(false);
        $this->setForm($form);

        parent::_prepareForm();
    }
}