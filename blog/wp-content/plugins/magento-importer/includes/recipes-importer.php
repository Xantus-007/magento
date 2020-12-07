<?php

/**
 * Class RecipesImporter.
 */
class RecipesImporter extends WP_CLI_Command
{
    private $currentLang = null;
    protected $redirections = [];
    protected $newBlogHomeUrl = 'http://monbento-blog.dbm-dev.com/';
    protected $magentoMediaPath = '/var/www/html/monbento/media/share/';
    protected $magentoRecipePagePath = 'club/index/detail/id/';
    protected $languages = ['fr_fr', 'en_gb'];
    protected $fileName = 'recipes_export_#LANG#.csv';
    protected $fileColumns = [
        'id' => 0,
        'lang' => 1,
        'title' => 2,
        'price' => 3,
        'difficulty' => 4,
        'likes' => 5,
        'recipe_time' => 6,
        'recipe_time_unit' => 7,
        'recipe_cooking_time' => 8,
        'recipe_cooking_time_unit' => 9,
        'categories' => 10,
        'photo' => 11,
        'legend' => 12,
        'ingredients' => 13,
        'recipe' => 14,
        'author' => 15,
        'date' => 16
    ];
    
    // old magento cat id => new wp cat id
    protected $catsAssocFr = [
        1 => 76, // Voyages
        3 => 76, // Populaires
        4 => 76, // Insolites
        5 => 6, // Entrées
        6 => 7, // Plats
        7 => 5, // Desserts & goûters
        10 => 76, // Kawaii
        11 => 76, // Recettes de chef
        12 => 76, // Au travail
        13 => 76, // Loisirs
        14 => 76, // Les meilleurs spots
        15 => 76, // Spécialités Françaises
        16 => 2, // Healthy
        17 => 76, // Authentiques
        18 => 245, // Pour les pressés
        19 => 76, // Exotiques
        20 => 243, // Kids
        21 => 72, // Boissons
        22 => 10, // Végétariennes
    ];//68,74
    protected $catsAssocEn = [
        1 => 119, // Voyages
        3 => 119, // Populaires
        4 => 119, // Insolites
        5 => 485, // Entrées
        6 => 479, // Plats
        7 => 477, // Desserts & goûters
        10 => 119, // Kawaii
        11 => 119, // Recettes de chef
        12 => 119, // Au travail
        13 => 119, // Loisirs
        14 => 119, // Les meilleurs spots
        15 => 119, // Spécialités Françaises
        16 => 483, // Healthy
        17 => 119, // Authentiques
        18 => 481, // Pour les pressés
        19 => 119, // Exotiques
        20 => 487, // Kids
        21 => 121, // Boissons
        22 => 880, // Végétariennes
    ];
    
    // user emails
    protected $userEmails = [
        '39104' => 'app@monbento.com',
        '36707' => 'frederic.boreal@orange.fr'
    ];
    
    // unit durations
    protected $unitDurations = [
        1 => [
            'short' => 'M',
            'long_fr' => 'minutes',
            'long_en' => 'minutes'
        ],
        2 => [
            'short' => 'H',
            'long_fr' => 'heures',
            'long_en' => 'hours'
        ]
    ];

    /**
     * Import receipes
     * ---
     * default: success
     * options:
     *   - success
     *   - error
     * ---
     * ## EXAMPLES
     *      wp recipes-importer import
     * 
     * @when after_wp_load
     */
    public function import($args, $assoc_args)
    {
        $recipes = [];
        foreach ($this->languages as $lang) {
            $this->currentLang = $lang;
            $newRecipes = $this->importFile(str_replace('#LANG#', $lang, $this->fileName));
            $recipes = array_merge($recipes, $newRecipes);
        }
        $this->importRecipes($recipes);
        $this->generateRewriteRuleFile();
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
     *      wp recipes-importer createAuthors
     * 
     * @when after_wp_load
     */
    public function createAuthors($args, $assoc_args)
    {
        //39104
        $this->createUser('Monbento', 'app@monbento.com');
        //36707
        $this->createUser('Frédéric Coursol', 'frederic.boreal@orange.fr');
    }

    /**
     * Import file.
     * @param string $file
     * @return array
     */
    protected function importFile($file)
    {
        return $this->readFile($file);
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
        $recipes = [];
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
                    $recipes[] = $row;
                }
            }
            fclose($fileHandle);
        }
        return $recipes;
    }

    /**
     * Parse file row to build post data.
     * @param array $row
     * @return array
     */
    protected function parseFileRow($row)
    {
        $userId = username_exists($this->userEmails[trim($row[$this->fileColumns['author']])]);
        if ($userId) {
            $recipeCategories = $this->convertCategories($row[$this->fileColumns['categories']]);
            if (!count($recipeCategories)) {
                $recipeCategories = [76];
            }
            $currentLang = explode('_', $row[$this->fileColumns['lang']])[0];
            $recipe = [
                'post_author' => $userId,
                'post_title' => $row[$this->fileColumns['title']],
                'post_name' => $row[$this->fileColumns['urlkey']],
                'post_content' => '',
                'post_excerpt' => '',
                'post_status' => 'publish',
                'post_date_gmt' => $row[$this->fileColumns['date']],
                'comment_status' => 1,
                'post_type' => 'post',
                'meta_input' => [
                    'penci_recipe_title' => $row[$this->fileColumns['title']],
                    'penci_recipe_preptime' => $row[$this->fileColumns['recipe_time']] . ' ' . $this->unitDurations[$row[$this->fileColumns['recipe_time_unit']]]['long_' . $currentLang],
                    'penci_recipe_preptime_format' => $row[$this->fileColumns['recipe_time']] . $this->unitDurations[$row[$this->fileColumns['recipe_time_unit']]]['short'],
                    'penci_recipe_cooktime' => $row[$this->fileColumns['recipe_cooking_time']] . ' ' . $this->unitDurations[$row[$this->fileColumns['recipe_cooking_time_unit']]]['long_' . $currentLang],
                    'penci_recipe_cooktime_format' => $row[$this->fileColumns['recipe_cooking_time']] . $this->unitDurations[$row[$this->fileColumns['recipe_cooking_time_unit']]]['short'],
                    'penci_recipe_ingredients' => $row[$this->fileColumns['ingredients']],
                    'penci_recipe_instructions' => $row[$this->fileColumns['recipe']]
                ],
                'extra_data' => [
                    'orig_id' => $row[$this->fileColumns['id']],
                    'categories' => $recipeCategories,
                    'photo' => str_replace($this->magentoMediaPath, '', $row[$this->fileColumns['photo']]),
                    'lang' => $currentLang
                ]
            ];
            return $recipe;
        }
        echo 'IGNORED RECIPE : ' . $row[$this->fileColumns['id']] . "\r\n";
        return;
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
     * @param array $recipes
     */
    protected function importRecipes($recipes)
    {
        $recipeTranslation = [];
        if (count($recipes)) {
            foreach ($recipes as $recipe) {
                $recipeExtraData = $recipe['extra_data'];
                $categories = $recipeExtraData['categories'];
                $photo = $recipeExtraData['photo'];
                unset($recipe['extra_data']);
                $postId = wp_insert_post($recipe, true);
                if ($postId) {
                    if (function_exists('pll_set_post_language')) {
                        if (!isset($recipeTranslation[$recipeExtraData['orig_id']])) {
                            $recipeTranslation[$recipeExtraData['orig_id']] = [];
                        }
                        pll_set_post_language($postId, $recipeExtraData['lang']);
                        $recipeTranslation[$recipeExtraData['orig_id']][$recipeExtraData['lang']] = $postId;
                    }
                    if (function_exists('pll_save_post_translations') && 
                        isset($recipeTranslation[$recipeExtraData['orig_id']]) &&
                        count($recipeTranslation[$recipeExtraData['orig_id']]) == 2) {
                        pll_save_post_translations($recipeTranslation[$recipeExtraData['orig_id']]);
                    }
                    $this->addRewriteRule($postId, $recipeExtraData);
                    wp_set_object_terms($postId, $categories, 'category');
                    $this->associateThumbnail($postId, $photo);
                } else {
                    echo 'ERROR IMPORT ' . $recipe['post_name'] . "\r\n";
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
        $newCategories = [76];
        $catsAssoc = $this->catsAssocFr;
        if ($this->currentLang == 'en') {
            $newCategories = [119];
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
        $file = $upload_dir['basedir']. '/share/' . $imageName;
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
     * @param array $recipeInfo
     */
    protected function addRewriteRule($postId, $recipeInfo)
    {
        $oldLink = $this->magentoRecipePagePath . $recipeInfo['orig_id'];
        $newLink = get_permalink($postId);
        if (!isset($this->redirections[$recipeInfo['lang']])) {
            $this->redirections[$recipeInfo['lang']] = [];
        }
        $this->redirections[$recipeInfo['lang']][] = 'RewriteRule ^' . $oldLink . '$ ' . $newLink . " [L,QSA,R=301]\r\n";
    }
    
    /**
     * Generate rewrite rules files.
     */
    protected function generateRewriteRuleFile()
    {
        if (!empty($this->redirections)) {
            $upload_dir = wp_upload_dir();
            $fileLang = $file = null;
            foreach ($this->redirections as $lang => $redirections) {
                if ($fileLang != $lang) {
                    $fileLang = $lang;
                    if ($file) {                        
                        fclose($file);
                    }
                    $file = fopen($upload_dir['basedir'] . '/redirections/redirections_recipes_keep_' . $lang . '.txt', 'w');
                }
                if ($file &&
                    !empty($redirections)) {
                    foreach ($redirections as $redirection) {
                        fwrite($file, $redirection);
                    }
                }
            }
            if ($file) {
                fclose($file);
            }
        }
    }

}
