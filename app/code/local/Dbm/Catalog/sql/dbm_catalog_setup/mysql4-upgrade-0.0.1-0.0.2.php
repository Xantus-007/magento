<?php

$installer = $this;

$installer->addAttribute('catalog_category', 'mobile_image', array(
    'type'              => 'varchar',
    'backend'           => 'catalog/category_attribute_backend_image',
    'frontend'          => '',
    'label'             => 'Mobile picture',
    'input'             => 'image',
    'class'             => '',
    'source'            => '',
    'global'            => 0,
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 0,
    'default'           => '',
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
    'position'          => 1,
));