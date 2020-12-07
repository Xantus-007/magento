<?php

require_once('abstract.php');

class Dbm_Shell_RewriteUrl extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $resource = $write->query("SELECT * FROM `core_url_rewrite` WHERE target_path LIKE '%shop-1%'");
        
        $data = $resource->fetchAll();
        
        foreach($data as $line)
        {
            echo $line['url_rewrite_id']."\n";
            $write->query("DELETE FROM `core_url_rewrite` WHERE url_rewrite_id = ".$line['url_rewrite_id']."");
        }
        
        echo 'END'."\n";
        exit();
    }
}


$shell = new Dbm_Shell_RewriteUrl();
$shell->run();