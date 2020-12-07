<?php

require_once('abstract.php');

class Dbm_Shell_NewBlogExportAllArticles extends Mage_Shell_Abstract
{

    public function run()
    {
        $allStores = Mage::app()->getStores();
        
        foreach ($allStores as $storeId => $store) {
            $templateProcessor = Mage::helper('cms')->getPageTemplateProcessor();
            $file = fopen(Mage::getBaseDir() . '/var/export/articles_all_export_store_' . $store->getCode() . '.csv', 'w');
            fputcsv($file, [
                'ID', 'StoreId', 'Titre', 'Clé d\'URL',
                'Catégories', 'Status',
                'Commentaires actifs', 'Mots clés',
                'Description courte', 'Description',
                'Photo', 'Auteur', 'Date',
                'Meta mots clés', 'Meta description']
            );
            $collection = Mage::getModel('blog/blog')->getCollection()
                        ->addStoreFilter($storeId)
                        ->setOrder('created_time ', 'desc');   
            $postsCategories = $this->getPostsCategories($collection, $storeId);
            foreach ($collection as $article) {
                $this->setPostCategories($postsCategories, $article);
                $photo = $article->getPostImage();
                $photoPath = '';
                if ($photo) {
                    $photoPath = Mage::getBaseDir('media') . '/blog/' . $photo;
                }
                $line = [
                    $article->getId(),
                    $storeId,
                    $article->getTitle(),
                    $article->getIdentifier(),
                    implode('#', $article->getCats()),
                    $article->getStatus(),
                    $article->getComments(),
                    $article->getTags(),
                    $templateProcessor->filter($article->getShortContent()),
                    $templateProcessor->filter($article->getPostContent()),
                    $photoPath,
                    $article->getUser(),
                    $article->getCreatedTime(),
                    $article->getMetaKeywords(),
                    $article->getMetaDescription()
                ];
                fputcsv($file, $line);
            }
            fclose($file);            
        }
    }

    public function getPostsCategories($collection, $storeId)
    {
        $posts = array();
        foreach ($collection as $item) {
            $posts[] = $item->getId();
        }
        $categories = $this->getCategoriesForArticles($posts, $storeId);

        foreach ($categories as &$category) {
            $category['posts'] = explode(',', $category['posts']);
            $category['data'] = $category['cat_id'];
        }
        return $categories;
    }
    
    public function getCategoriesForArticles($posts, $storeId) 
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        
        $select = $readConnection
            ->select()
            ->from(array('post_category' => $resource->getTableName('blog/post_cat')))
            ->joinLeft(
                array('category_store' => $resource->getTableName('blog/cat_store')),
                'post_category.cat_id = category_store.cat_id', array()
            )
            ->joinLeft(
                array('category_main' => $resource->getTableName('blog/cat')), 'post_category.cat_id = category_main.cat_id',
                array('title', 'identifier', 'posts' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT post_id)'))
            )
            ->where('category_store.store_id = ?', $storeId)
            ->where('post_category.post_id IN(?)', $posts)
            ->group('category_main.cat_id')
        ;

        return $readConnection->fetchAll($select);
    }

    public function setPostCategories($categories, $post)
    {
        $categoriesData = array();
        foreach ($categories as $catsScope) {
            if (is_array($catsScope['posts'])) {
                if (in_array($post->getId(), $catsScope['posts'])) {
                    $categoriesData[] = $catsScope['data'];
                }
            }
        }
        $post->setCats($categoriesData);
    }

}

$shell = new Dbm_Shell_NewBlogExportAllArticles();
$shell->run();
