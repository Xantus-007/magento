<?php

$resourceIds = array('cartsguru', 'cartsguru/admin', 'catalog', 'catalog/product', 'catalog/product/info', 'catalog/product/lsit');
$roleName = 'cartsguru';

//check if cartsguru api role already exists
$match = false;
$cartsguruApiRoleCollection = Mage::getModel('api/role')
    ->getCollection()
    ->addFieldToFilter('role_name', $roleName);

foreach ($cartsguruApiRoleCollection as $cartsguruApiRole) {
    $rulesCollection = Mage::getModel('api/rules')->getCollection()
        ->addFieldToFilter('role_id', $cartsguruApiRole->getRoleId())
        ->addFieldToFilter('resource_id', array('in' => $resourceIds))
        ->addFieldToFilter('api_permission', 'allow');
    if ($rulesCollection->count() > 0) {
        $match = true;
    }
}
if (!$match) {
    $apiRole = Mage::getModel('api/role');

    $apiRole
        ->setRoleName($roleName)
        ->setTreeLevel(1)
        ->setRoleType('G')
        ->save();
    $rules = Mage::getModel('api/rules')
        ->setRoleId($apiRole->getRoleId())
        ->setResources($resourceIds)
        ->saveRel();
}