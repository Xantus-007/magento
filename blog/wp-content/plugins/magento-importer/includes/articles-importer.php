<?php

/**
 * Class ArticlesImporter.
 */
class ArticlesImporter extends WP_CLI_Command
{
    private $currentLang = null;
    protected $redirections = [];
    protected $magentoMediaWysiwygUrls = [
        'https://en.monbento.com/media/', 
        'http://en.monbento.com/media/', 
        'http://www.monbento.com/media/', 
        'https://www.monbento.com/media/'];
    protected $magentoMediaPath = '/var/www/html/monbento/media/blog/';
    protected $magentoArticlePagePath = 'club/';
    protected $languages = ['fr', 'en'];
    protected $fileName = 'articles_export_#LANG#.csv';
    protected $fileColumns = [
        'id' => 0,
        'lang' => 1,
        'title' => 2,
        'urlkey' => 3,
        'categories' => 4,
        'status' => 5,
        'comments_status' => 6,
        'key_words' => 7,
        'short_desc' => 8,
        'desc' => 9,
        'photo' => 10,
        'author' => 11,
        'date' => 12,
        'meta_key_words' => 13,
        'meta_desc' => 14
    ];    
    protected $userEmails = [
        'Agathe' => 'guest.agathe@monbento.com',
        'Alix' => 'guest.alix@monbento.com',
        'Anthony' => 'guest.anthony@monbento.com',
        'Arnaud D' => 'guest.arnaud@monbento.com',
        'Arnaud' => 'guest.arnaud@monbento.com',
        'Camille' => 'camille.tamizier@monbento.com',
        'Capucine' => 'capucine@monbento.com',
        'capucine' => 'capucine@monbento.com',
        'Capucine Levai' => 'capucine@monbento.com',
        'capucine Levai' => 'capucine@monbento.com',
        'Emilie' => 'emilie.creuzieux@monbento.com',
        'Fanny' => 'guest.fanny@monbento.com',
        'Julie' => 'julie@monbento.com',
        'Justine Guilhem' => 'guest.justine@monbento.com',
        'Justine' => 'guest.justine@monbento.com',
        'Lucie' => 'guest.lucie@monbento.com',
        'Manon' => 'guest.manon@monbento.com',
        'Morgane' => 'guest.morgane@monbento.com',
        'Nawel' => 'guest.nawel@monbento.com',
        'Sophia' => 'guest.sophia@monbento.com',
        'Synthia' => 'synthia@monbento.com',
        'Thomas' => 'thomas.manuby@monbento.com',
        'monbento' => 'agence@monbento.com',
        'synthia perrier' => 'synthia@monbento.com'
    ];
    
    // old magento cat id => new wp cat id
    protected $catsAssocFr = [
        3 => 78, // Recettes bento 
        4 => 84, // Actu monbento 
        5 => 78, // bento recipes 
        6 => 84, // monbento news
        7 => 84, // Nouveautés mon bento 
        8 => 82, // new products
        9 => 78, // Culture japonaise 
        10 => 78, // Japan lifestyle 
        11 => 78, // la vie à monbento
        12 => 80, // kids
        13 => 80, // kids
        14 => 80 // kids
    ];
    protected $catsAssocEn = [
        3 => 125, // Recettes bento 
        4 => 490, // Actu monbento 
        5 => 125, // bento recipes 
        6 => 490, // monbento news
        7 => 490, // Nouveautés mon bento 
        8 => 492, // new products
        9 => 125, // Culture japonaise 
        10 => 125, // Japan lifestyle 
        11 => 125, // la vie à monbento
        12 => 494, // kids
        13 => 494, // kids
        14 => 494 // kids
    ];

    /**
     * Import articles
     * ---
     * default: success
     * options:
     *   - success
     *   - error
     * ---
     * ## EXAMPLES
     *      wp articles-importer import
     * 
     * @when after_wp_load
     */
    public function import($args, $assoc_args)
    {
        foreach ($this->languages as $lang) {
            $this->currentLang = $lang;
            $this->importFile(str_replace('#LANG#', $lang, $this->fileName));
        }
        $this->generateRewriteRuleFile();
    }

    /**
     * List authors
     * ---
     * default: success
     * options:
     *   - success
     *   - error
     * ---
     * ## EXAMPLES
     *      wp articles-importer listAuthors
     * 
     * @when after_wp_load
     */
    public function listAuthors($args, $assoc_args)
    {
        $articles = [];
        foreach ($this->languages as $lang) {
            $articles = $this->readFile(str_replace('#LANG#', $lang, $this->fileName), true);
        }
        $authors = $this->getDistinctAuthors($articles);
        if (count($authors)) {
            sort($authors);
            foreach ($authors as $author) {
                echo $author . "\r\n";
            }
        }
    }    

    /**
     * Create authors
     * ---
     * default: success
     * options:
     *   - success
     *   - error
     * ---
     * ## EXAMPLES
     *      wp articles-importer createAuthors
     * 
     * @when after_wp_load
     */
    public function createAuthors($args, $assoc_args)
    {        
        $articles = [];
        foreach ($this->languages as $lang) {
            $articles = $this->readFile(str_replace('#LANG#', $lang, $this->fileName), true);
        }
        $authors = $this->getDistinctAuthors($articles);
        if (count($authors)) {
            sort($authors);
            foreach ($authors as $author) {
                if (isset($this->userEmails[$author])) {
                    $this->createUser($author, $this->userEmails[$author]);
                }
            }
        }
    }

    /**
     * Import file.
     * @param string $file
     */
    protected function importFile($file)
    {
        $articles = $this->readFile($file);
        $this->importArticles($articles);
    }

    /**
     * Read CSV file.
     * @param string $file
     * @param boolean $origData
     * @return array
     */
    protected function readFile($file, $origData = false)
    {
        $uploadDirInfo = wp_upload_dir();
        $filePath = $uploadDirInfo['basedir'] . '/data/' . $file;
        $articles = [];
        if (is_file($filePath)) {
            $fileHandle = fopen($filePath, 'r');
            $rowCount = 0;
            while (($row = fgetcsv($fileHandle)) !== false) {
                $rowCount++;
                if ($rowCount === 1) {
                    continue;
                }
                if (!$origData) {
                    $row = $this->parseFileRow($row);
                }
                if ($row) {
                    $articles[] = $row;
                }
            }
            fclose($fileHandle);
        }
        return $articles;
    }

    /**
     * Parse file row to build post data.
     * @param array $row
     * @return array
     */
    protected function parseFileRow($row)
    {
        $userId = username_exists('app@monbento.com');
        if ($userId) {
            $articleCategories = $this->convertCategories($row[$this->fileColumns['categories']]);     
            $description = $row[$this->fileColumns['desc']];
            foreach ($this->magentoMediaWysiwygUrls as $wysiwygUrl) {
                $description = str_replace($wysiwygUrl, wp_upload_dir()['baseurl'] . '/', $description);
            }
            $article = [
                'post_author' => $userId,
                'post_title' => $row[$this->fileColumns['title']],
                'post_name' => $row[$this->fileColumns['urlkey']],
                'post_content' => $description,
                'post_excerpt' => strip_tags($row[$this->fileColumns['short_desc']]),
                'post_status' => $row[$this->fileColumns['status']] == 1 ? 'publish' : 'draft',
                'post_date_gmt' => $row[$this->fileColumns['date']],
                'comment_status' => $row[$this->fileColumns['comments_status']] ? 'open' : 'closed',
                'post_type' => 'portfolio',
                'extra_data' => [
                    'categories' => $articleCategories,
                    'photo' => str_replace($this->magentoMediaPath, '', $row[$this->fileColumns['photo']])
                ]
            ];
            if (!empty($row[$this->fileColumns['meta_desc']])) {
                $article['meta_input'] = [
                    'yoast_wpseo_metadesc' => $row[$this->fileColumns['meta_desc']]
                ];
            }
            return $article;
        }
        echo 'IGNORED ARTICLE : ' . $row[$this->fileColumns['id']] . "\r\n";
        return;
    }

    /**
     * Get distinct authors.
     * @param array $articles
     */
    protected function getDistinctAuthors($articles)
    {
        $authors = [];
        if (count($articles)) {
            foreach ($articles as $article) {
                $authors[] = trim($article[$this->fileColumns['author']]);
            }
        }
        return array_unique($authors);
    }

    /**
     * Create WP user.
     * @param string $displayName
     * @param string $email
     * @return int
     */
    protected function createUser($displayName, $email)
    {
        $userId = username_exists($email);
        if (null == $userId) {
            $password = wp_generate_password(12, false);
            $userId = wp_create_user($email, $password, $email);
            wp_update_user([
                'ID' => $userId,
                'display_name' => $displayName
            ]);
            $user = new WP_User($userId);
            $user->set_role('author');
        }
        
        return $userId;
    }
    
    /**
     * Import articles into WP.
     * @param array $articles
     */
    protected function importArticles($articles)
    {
        if (count($articles)) {
            foreach ($articles as $article) {
                $categories = $article['extra_data']['categories'];
                $photo = $article['extra_data']['photo'];
                unset($article['extra_data']);
                $postId = wp_insert_post($article, true);
                if ($postId) {
                    if (function_exists('pll_set_post_language')) {
                        pll_set_post_language($postId, $this->currentLang);                        
                    }
                    $this->addRewriteRule($postId, $article);
                    wp_set_object_terms($postId, $categories, 'portfolio-category');
                    $this->associateThumbnail($postId, $photo);
                } else {
                    echo 'ERROR IMPORT ' . $article['post_name'] . "\r\n";
                }
            }
        }
    }
    
    /**
     * Convert old categories id to new.
     * @param string $categories
     * @return array
     */
    protected function convertCategories($categories)
    {
        $newCategories = [78];
        $catsAssoc = $this->catsAssocFr;
        if ($this->currentLang == 'en') {
            $newCategories = [125];
            $catsAssoc = $this->catsAssocEn;
        }
        if (!empty($categories)) {
            $categoriesList = explode('#', $categories);
            if (count($categoriesList)) {
                foreach ($categoriesList as $oldCategory) {
                    if (isset($catsAssoc[$oldCategory])) {
                        $newCategories[] = $catsAssoc[$oldCategory];
                    }
                }
            }
        }
        return array_unique($newCategories);
    }

    /**
     * Associate thumbnail to post.
     * @param int $postId
     * @param string $imageName
     * @return void
     */
    protected function associateThumbnail($postId, $imageName)
    {
        if (empty($imageName)) {
            return;
        }
        $upload_dir = wp_upload_dir();
        $file = $upload_dir['basedir']. '/blog/' . $imageName;
        $filename = basename($file);
        $wp_filetype = wp_check_filetype($filename, null);
        if (file_exists($file) && $wp_filetype['type']) {
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attachId = wp_insert_attachment($attachment, $file, $postId);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachData = wp_generate_attachment_metadata($attachId, $file);
            wp_update_attachment_metadata($attachId, $attachData);
            set_post_thumbnail($postId, $attachId);
        }
    }
    
    /**
     * Add rewrite rule to generate.
     * @param int $postId
     * @param array $articleInfo
     */
    protected function addRewriteRule($postId, $articleInfo)
    {
        $oldLink = $this->magentoArticlePagePath . $articleInfo['post_name'];
        $newLink = get_permalink($postId);
        $this->redirections[] = 'RewriteRule ^' . $oldLink . '$ ' . $newLink . " [L,QSA,R=301]\r\n";
    }
    
    /**
     * Generate rewrite rules files.
     */
    protected function generateRewriteRuleFile()
    {
        if (!empty($this->redirections)) {
            $upload_dir = wp_upload_dir();
            $file = fopen($upload_dir['basedir'] . '/redirections/redirections_articles_keep.txt', 'w');
            foreach ($this->redirections as $redirection) {
                fwrite($file, $redirection);
            }
            fclose($file);
        }
    }

}
