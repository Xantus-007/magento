<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogExportRecipes extends Mage_Shell_Abstract
{
    private $languages = ['fr_fr', 'en_gb', 'en_ie', 'ja_jp', 'es_es', 'pt_pt'];
    
    public function run()
    {
        $collection = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter(Dbm_Share_Model_Element::TYPE_PHOTO)
            ->orderByDate()
        ;
        
        $helper = Mage::helper('dbm_share');
        foreach ($this->languages as $lang) {
            $file = fopen(Mage::getBaseDir() . '/var/export/photos_all_export_' . $lang . '.csv', 'w');
            fputcsv($file, [
                'ID', 'Langue', 'Titre', 'Likes',
                'Catégories', 'Photo', 'Auteur', 'Date']
            );
            foreach ($collection as $photoClub) {
                if (empty($photoClub->getData('title_' . $lang))) {
                    continue;
                }
                $photo = $photoClub->getPhotos()->getFirstItem();
                $photoPath = Mage::getBaseDir('media') . '/' .
                    Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER . '/' . 
                    Dbm_Share_Model_Photo::MEDIA_FOLDER . '/' . 
                    $helper->getPhotoDir($photo->getFilename(), false, '/') . 
                    $photo->getFilename();
                
                $line = [
                    $photoClub->getId(),
                    $lang,
                    $photoClub->getData('title_' . $lang),
                    $photoClub->getLikes(),
                    implode('#', array_column($photoClub->getCategories()->getData(), 'id')),
                    $photoPath,
                    $photoClub->getIdCustomer(),
                    $photoClub->getCreatedAt()
                ];
                
                fputcsv($file, $line);
            }
            fclose($file);
        }
    }
}


$shell = new Dbm_Shell_NewBlogExportRecipes();
$shell->run();