<?php

class Dbm_Utils_Helper_Index extends Mage_Core_Helper_Abstract
{

    public function reindexAll()
    {
        $processes = array();
        $indexer = Mage::getSingleton('index/indexer');

        //reindex all and set indexes as they were
        foreach ($indexer->getProcessesCollection() as $process) {
            $process->reindexEverything();
            $process->setData('mode', Mage_Index_Model_Process::MODE_REAL_TIME)->save();
        }
    }
}