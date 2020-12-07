<?php

$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');
$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();

/* Ajout jeu d'attributs Faq */
$modelFaq = Mage::getModel('eav/entity_attribute_set')->setEntityTypeId($entityTypeId);
$modelFaq->setAttributeSetName('Faq');
$modelFaq->validate();
$modelFaq->save();
$attributeSetIdFaq = $modelFaq->getAttributeSetId();

/* Ajout des attributs pour le post Faq */
$installerProduct->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, 'question_recurrente', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'default' => 0,
    'label' => 'Question récurrente',
    'backend' => '',
    'is_visible' => 1,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'is_user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

/* Ajout groupe General pour le jeu d'attributs Faq */
$installerProduct->addAttributeGroup($entityTypeId, $attributeSetIdFaq, 'General', 100);
$attributeGroupId = $installerProduct->getAttributeGroupId($entityTypeId, $attributeSetIdFaq, 'General');

$attributesForFaq = array('meta_description', 'meta_keywords', 'meta_title', 'post_title', 'post_content', 'question_recurrente', 'status', 'url_key');
foreach($attributesForFaq as $attribute)
{
    $attributeId = $installerProduct->getAttributeId(Mageplaza_BetterBlog_Model_Post::ENTITY, $attribute);
    $installerProduct->addAttributeToSet(
        Mageplaza_BetterBlog_Model_Post::ENTITY, 
        $attributeSetIdFaq, 
        $installerProduct->getDefaultAttributeGroupId(Mageplaza_BetterBlog_Model_Post::ENTITY, $attributeSetIdFaq), 
        $attributeId
    );
}

$installerProduct->endSetup();