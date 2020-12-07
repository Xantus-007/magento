<?php

class Dbm_Store_Model_Location extends Monbento_StoreLocator_Model_Rewrite_Unirgy_Location
{
    public function toApiArray()
    {
        return array(
            'id' => $this->getLocationId(),
            'title' => $this->getTitle(),
            'address' => $this->getAddress(),
            'lat' => $this->getLatitude(),
            'lng' => $this->getLongitude(),
            'phone' => $this->getPhone()
        );
    }
}