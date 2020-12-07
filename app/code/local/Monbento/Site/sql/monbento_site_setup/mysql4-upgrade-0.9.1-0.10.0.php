<?php 

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

/* Ajout jeu d'attributs Presse */
$modelPresse = Mage::getModel('eav/entity_attribute_set')->setEntityTypeId($entityTypeId);
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();

$installer = Mage::getResourceModel('mageplaza_betterblog/setup', 'mageplaza_betterblog_setup');
$installer->startSetup();
/* @var $installer Mageplaza_BetterBlog_Model_Resource_Setup */
    
$titleArray = array(
    'post_title' => array(
        'position' => 10,
        'titre' => 'Titre'
    ),
    'texte_2' => array(
        'position' => 40,
        'titre' => 'Texte en-tête'
    ),
    'texte_3' => array(
        'position' => 41,
        'titre' => 'Texte sous-titre'
    ),
    'texte_4' => array(
        'position' => 42,
        'titre' => 'Texte variante'
    )
);

foreach ($titleArray as $key => $title) {
    $installer->removeAttribute('mageplaza_betterblog_post', $key . '_typo');
    $installer->removeAttribute('mageplaza_betterblog_post', $key . '_color');
    $installer->removeAttribute('mageplaza_betterblog_post', $key . '_size');
    
    $installer->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, $key . '_typo', array(
        'group' => 'General',
        'type' => 'int',
        'input' => 'select',
        'label' => 'Typo (' . $title['titre'] . ')',
        'position' => $title['position'],
        'is_visible' => 1,
        'default' => 0,
        'source' => 'monbento_site/adminhtml_source_selecttypodefault',
        'required' => false,
        'is_user_defined' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
    ))
    ->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, $key . '_color', array(
        'group' => 'General',
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Couleur (' . $title['titre'] . ')',
        'position' => $title['position'],
        'note' => 'Code hexadécimal',
        'is_visible' => 1,
        'source' => '',
        'is_user_defined' => 0,
        'required' => false,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
    ))
    ->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, $key . '_size', array(
        'group' => 'General',
        'type' => 'int',
        'input' => 'select',
        'label' => 'Taille (' . $title['titre'] . ')',
        'position' => $title['position'],
        'is_visible' => 1,
        'default' => 0,
        'source' => 'monbento_site/adminhtml_source_selectsizedefault',
        'is_user_defined' => 0,
        'required' => false,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
    ));
}
$installer->endSetup();