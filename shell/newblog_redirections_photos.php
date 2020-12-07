<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogRedirectionsPhotos extends Mage_Shell_Abstract
{

    protected $newBlogHomeUrl = 'http://monbento-blog.dbm-dev.com/';
    public function run()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter(Dbm_Share_Model_Element::TYPE_PHOTO)
            ->orderByDate()
        ;

        $file = fopen(Mage::getBaseDir() . '/var/export/redirections_photos.txt', 'w');
        foreach ($collection as $photo) {
            $redirection = 'RewriteRule ^club/index/detail/id/' . $photo->getId() . '$ ' . $this->newBlogHomeUrl . " [L,QSA,R=301]\r\n";
            fwrite($file, $redirection);
        }
        fclose($file);
    }

}

$shell = new Dbm_Shell_NewBlogRedirectionsPhotos();
$shell->run();
