<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogRedirectionsRecipesAuthor extends Mage_Shell_Abstract
{        
    private $customerIds = [39104, 36707];
    
    protected $newBlogHomeUrl = 'https://en.monbento.com/blog/';
    protected $newBlogHomeUrlFr = 'https://www.monbento.com/blog/';
    
    public function run()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter(Dbm_Share_Model_Element::TYPE_RECEIPE)
            ->orderByDate()
        ;

        $file = fopen(Mage::getBaseDir() . '/var/export/redirections_recipes_author.txt', 'w');
        foreach ($collection as $recipe) {
            if (in_array($recipe->getIdCustomer(), $this->customerIds)) {
                continue;
            }
            $redirection = 'RewriteRule ^club/index/detail/id/' . $recipe->getId() . '$ ' . $this->newBlogHomeUrl . " [L,QSA,R=301]\r\n";
            if (!empty(trim($recipe->getData('ingredients_content_fr_fr')))) {
                $redirection = 'RewriteRule ^club/index/detail/id/' . $recipe->getId() . '$ ' . $this->newBlogHomeUrlFr . " [L,QSA,R=301]\r\n";
            }
            fwrite($file, $redirection);
        }
        fclose($file);
    }

}

$shell = new Dbm_Shell_NewBlogRedirectionsRecipesAuthor();
$shell->run();
