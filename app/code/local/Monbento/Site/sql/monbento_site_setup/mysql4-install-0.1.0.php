<?php

/* Ajout/Suppression des attributs de catégorie */
$installerCategory = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerCategory->removeAttribute('catalog_category','legend');
$installerCategory->removeAttribute('catalog_category','visuel_1_url');
$installerCategory->removeAttribute('catalog_category','visuel_2_url');
$installerCategory->removeAttribute('catalog_category','img_shop');
$installerCategory->removeAttribute('catalog_category','img_shop_hover');

$installerCategory->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'select_colori_for_product', array(
    'group' => 'General Information',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Afficher le select colori sur les pages produits',
    'backend' => '',
    'visible' => true,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

for($i=1;$i<4;$i++)
{
    $installerCategory->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'select_slider_'.$i, array(
        'group' => 'General Information',
        'type' => 'int',
        'input' => 'select',
        'label' => 'Sélectionner le slider '.$i,
        'backend' => '',
        'visible' => true,
        'required' => false,
        'source' => 'monbento_site/attribute_source_sliderscategory',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
}

$installerCategory->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'description_shop', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Description pour la page shop',
    'backend' => '',
    'visible' => true,
    'required' => false,
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerCategory->endSetup();


/* Ajout des attributs de produit */
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerProduct->addAttributeGroup('catalog_product', 'Default', 'Mise en page Monbento', 1000);

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'baseline', array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Baseline',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'visuel_promotionnel', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Sticky promotionnel',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'option' => array(
        'values' => array(
            0 => 'Remise',
            1 => 'Soldes',
            2 => 'Nouveau',
        ),
    ),
    'user_defined' => 1,
    'used_in_product_listing' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'mention_promotionnelle', array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Mention promotionnelle',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'used_in_product_listing' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'select_for_shop', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'default' => 0,
    'label' => 'Afficher sur la page shop',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'user_defined' => 1,
    'used_in_product_listing' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'code_hexa', array(
    'group' => 'Mise en page Monbento',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Code hexadecimal de la page',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'code_hexa_bas', array(
    'group' => 'Mise en page Monbento',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Code hexadecimal 2',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

for($i=1;$i<4;$i++)
{
    $installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'select_reassurance_'.$i, array(
        'group' => 'Mise en page Monbento',
        'type' => 'int',
        'input' => 'select',
        'label' => 'Bloc de réassurance '.$i,
        'backend' => '',
        'visible' => 1,
        'required' => false,
        'source' => 'monbento_site/attribute_source_blocsreassurance',
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
}

for($i=1;$i<4;$i++)
{
    $installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'select_adopter_'.$i, array(
        'group' => 'Mise en page Monbento',
        'type' => 'int',
        'input' => 'select',
        'label' => 'Bloc l\'adopter '.$i,
        'backend' => '',
        'visible' => 1,
        'required' => false,
        'source' => 'monbento_site/attribute_source_blocsadopter',
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
}

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'display_bandeau_perso', array(
    'group' => 'Mise en page Monbento',
    'type' => 'int',
    'input' => 'select',
    'default' => 0,
    'label' => 'Afficher le bandeau de personnalisation',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'texte_bandeau_perso', array(
    'group'         => 'Mise en page Monbento',
    'type'          => 'text',
    'input'         => 'textarea',
    'default'       => '',
    'label'         => 'Texte du bandeau de personnalisation',
    'backend'       => '',
    'visible'    => 1,
    'required'      => false,
    'wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'html_allowed_on_front' => 1,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'titre_description', array(
    'group' => 'Mise en page Monbento',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Titre pour la description',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'texte_caracteristiques', array(
    'group'         => 'Mise en page Monbento',
    'type'          => 'text',
    'input'         => 'textarea',
    'default'       => '',
    'label'         => 'Texte des caractéristiques',
    'backend'       => '',
    'visible'    => 1,
    'required'      => false,
    'wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'html_allowed_on_front' => 1,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'texte_dimensions', array(
    'group'         => 'Mise en page Monbento',
    'type'          => 'text',
    'input'         => 'textarea',
    'default'       => '',
    'label'         => 'Texte des dimensions/compositions',
    'backend'       => '',
    'visible'    => 1,
    'required'      => false,
    'wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'html_allowed_on_front' => 1,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'visuel_bloc_caracteristiques', array(
    'group' => 'Images',
    'type' => 'varchar',
    'input' => 'media_image',
    'label' => 'Visuel du bloc des caractéristiques',
    'frontend' => 'catalog/product_attribute_frontend_image',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

for($i=1;$i<4;$i++)
{
    $installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'visuel_icone_presentation_'.$i, array(
        'group' => 'Images',
        'type' => 'varchar',
        'input' => 'media_image',
        'label' => 'Icone de présentation '.$i,
        'frontend' => 'catalog/product_attribute_frontend_image',
        'visible' => 1,
        'required' => false,
        'source' => '',
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
    
    $installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'titre_icone_presentation_'.$i, array(
        'group' => 'Mise en page Monbento',
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Titre de l\'icone de présentation '.$i,
        'backend' => '',
        'visible' => 1,
        'required' => false,
        'source' => '',
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
    
    $installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'texte_icone_presentation_'.$i, array(
        'group' => 'Mise en page Monbento',
        'type' => 'text',
        'input' => 'textarea',
        'label' => 'Texte de l\'icone de présentation '.$i,
        'backend' => '',
        'visible' => 1,
        'required' => false,
        'source' => '',
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
}

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'visuel_miseenavant', array(
    'group' => 'Images',
    'type' => 'varchar',
    'input' => 'media_image',
    'label' => 'Visuel de mise en avant',
    'frontend' => 'catalog/product_attribute_frontend_image',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));