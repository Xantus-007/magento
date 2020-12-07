<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogRedirectionsRecipesLang extends Mage_Shell_Abstract
{        
    private $languages = ['es_es' => 'www.monbento.es', 'ja_jp' => 'hk.monbento.com'];    
    
    private $customerIds = [39104, 36707];
    
    protected $newBlogHomeUrl = 'http://monbento-blog.dbm-dev.com/';
    
    public function run()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter(Dbm_Share_Model_Element::TYPE_RECEIPE)
            ->orderByDate()
        ;
        foreach ($this->languages as $lang => $domain) {
            $file = fopen(Mage::getBaseDir() . '/var/export/redirections_recipes_lang_' . $lang . '.txt', 'w');
            $firstRow = 'RewriteCond %{HTTP_HOST} !^' . $domain . "$ [NC]\r\n";
            $secondRow = 'RewriteRule ^ - ';
            $rows = [];
            foreach ($collection as $recipe) {
                if (!in_array($recipe->getIdCustomer(), $this->customerIds) ||
                    empty($recipe->getData('title_' . $lang))) {
                    continue;
                }
                $rows[] = 'RewriteRule ^club/index/detail/id/' . $recipe->getId() . '$ ' . $this->newBlogHomeUrl . " [L,QSA,R=301]\r\n";                
            }
            if (count($rows)) {
                $secondRow .= '[S=' . count($rows) . "]\r\n";
                fwrite($file, $firstRow);
                fwrite($file, $secondRow);
                fwrite($file, implode('', $rows));
            }
            fclose($file);
        }
    }

}

$shell = new Dbm_Shell_NewBlogRedirectionsRecipesLang();
$shell->run();