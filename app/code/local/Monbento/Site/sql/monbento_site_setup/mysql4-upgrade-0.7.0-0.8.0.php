<?php

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

/* Ajout jeu d'attributs Presse */
$modelPresse = Mage::getModel('eav/entity_attribute_set')->setEntityTypeId($entityTypeId);
$modelPresse->setAttributeSetName('Presse');
$modelPresse->validate();
$modelPresse->save();
$attributeSetIdPresse = $modelPresse->getAttributeSetId();

/* Ajout des attributs pour le post Presse */
$installerProduct->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, 'presse_type_tv', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'default' => 0,
    'label' => 'Image type TV',
    'backend' => '',
    'is_visible' => 1,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'is_user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$attributesForPresse = array('href', 'image', 'image_alt', 'meta_description', 'meta_keywords', 'meta_title', 'post_title', 'post_content', 'presse_type_tv', 'status', 'texte_2', 'url_key');
foreach($attributesForPresse as $attribute)
{
    $attributeId = $installerProduct->getAttributeId(Mageplaza_BetterBlog_Model_Post::ENTITY, $attribute);
    $installerProduct->addAttributeToSet(
        Mageplaza_BetterBlog_Model_Post::ENTITY, 
        $attributeSetIdPresse, 
        $installerProduct->getDefaultAttributeGroupId(Mageplaza_BetterBlog_Model_Post::ENTITY, $attributeSetIdPresse), 
        $attributeId
    );
}

/* Ajout jeu d'attributs Ambassadeurs */
$modelAmbassadeur = Mage::getModel('eav/entity_attribute_set')->setEntityTypeId($entityTypeId);
$modelAmbassadeur->setAttributeSetName('Ambassadeur');
$modelAmbassadeur->validate();
$modelAmbassadeur->save();
$attributeSetIdAmbassadeur = $modelAmbassadeur->getAttributeSetId();

/* Ajout groupe General pour le jeu d'attributs Ambassadeurs */
$installerProduct->addAttributeGroup($entityTypeId, $attributeSetIdAmbassadeur, 'General', 100);
$attributeGroupId = $installerProduct->getAttributeGroupId($entityTypeId, $attributeSetIdAmbassadeur, 'General');

/* Ajout des attributs pour le post Ambassadeur */
$attributesForAmbassadeur = array('image', 'image_alt', 'meta_description', 'meta_keywords', 'meta_title', 'post_title', 'post_content', 'status', 'texte_3', 'url_key');
foreach($attributesForAmbassadeur as $attribute)
{
    $attributeId = $installerProduct->getAttributeId(Mageplaza_BetterBlog_Model_Post::ENTITY, $attribute);
    $installerProduct->addAttributeToSet(
        Mageplaza_BetterBlog_Model_Post::ENTITY, 
        $attributeSetIdAmbassadeur, 
        $attributeGroupId, 
        $attributeId
    );
}