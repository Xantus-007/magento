<?php

abstract class Dbm_Share_Model_Timelogged_Abstract extends Dbm_Share_Model_Localized_Abstract
{

    /**
     * Auto set created / updated dates
     * @return Dbm_Share_Model_Timelogged_Abstract
     */
    protected function _beforeSave()
    {
        $now = Mage::app()->getLocale()->date()->toString('yyyy-MM-dd HH:mm:ss');

        if(!$this->getCreatedAt())
        {
            $this->setCreatedAt($now);
        }

        $this->setUpdatedAt($now);

        return parent::_beforeSave();
    }
}