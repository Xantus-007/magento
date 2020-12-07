<?php

require_once('abstract.php');

class Dbm_Birthday extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '1G');
        
        $mod = Mage::getModel('dbm_share/observer');
        $mod->birthdayCronHandler();
    }
}

$shell = new Dbm_Birthday();
$shell->run();
