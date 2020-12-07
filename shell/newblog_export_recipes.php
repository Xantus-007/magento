<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogExportRecipes extends Mage_Shell_Abstract
{
    private $languages = ['fr_fr', 'en_gb'];
        
    private $customerIds = [39104, 36707];
    
    public function run()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter(Dbm_Share_Model_Element::TYPE_RECEIPE)
            ->orderByDate()
        ;
        
        $helper = Mage::helper('dbm_share');
        foreach ($this->languages as $lang) {
            $file = fopen(Mage::getBaseDir() . '/var/export/recipes_export_' . $lang . '.csv', 'w');
            fputcsv($file, [
                'ID', 'Langue', 'Titre', 'Prix', 'Difficulté', 'Likes',
                'Durée recette', 'Unité durée recette', 
                'Durée cuisson', 'Unité durée cuisson', 
                'Catégories', 'Photo', 'Légende', 
                'Ingrédients', 'Recette', 'Auteur', 'Date']
            );
            foreach ($collection as $recipe) {
                if (!in_array($recipe->getIdCustomer(), $this->customerIds)) {
                    continue;
                }
                $photo = $recipe->getPhotos()->getFirstItem();
                $photoPath = Mage::getBaseDir('media') . '/' .
                    Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER . '/' . 
                    Dbm_Share_Model_Photo::MEDIA_FOLDER . '/' . 
                    $helper->getPhotoDir($photo->getFilename(), false, '/') . 
                    $photo->getFilename();
                
                $line = [
                    $recipe->getId(),
                    $lang,
                    $recipe->getData('title_' . $lang),
                    $recipe->getPrice(),
                    $recipe->getLevel(),
                    $recipe->getLikes(),
                    $recipe->getDuration(),
                    $recipe->getDurationUnit(),
                    $recipe->getCookingDuration(),
                    $recipe->getCookingDurationUnit(),
                    implode('#', array_column($recipe->getCategories()->getData(), 'id')),
                    $photoPath,
                    $recipe->getData('legend_' . $lang),
                    $recipe->getData('ingredients_content_' . $lang),
                    $recipe->getData('description_'. $lang),
                    $recipe->getIdCustomer(),
                    $recipe->getCreatedAt()
                ];
                
                fputcsv($file, $line);
            }
            fclose($file);
        }
    }
}


$shell = new Dbm_Shell_NewBlogExportRecipes();
$shell->run();