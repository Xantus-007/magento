<?php

/*
  Plugin Name: Magento Blog Importer
  Plugin URI: #
  Description: Import des contenus du blog depuis Magento.
  Version: 1.0
  Author: DBM
  Author URI: https://www.debussac.net
 */

define('MAGENTO_IMPORTER_PLUGIN', __FILE__);
define('MAGENTO_IMPORTER_PLUGIN_BASENAME', plugin_basename(MAGENTO_IMPORTER_PLUGIN));
define('MAGENTO_IMPORTER_PLUGIN_NAME', trim(dirname(MAGENTO_IMPORTER_PLUGIN_BASENAME), '/'));
define('MAGENTO_IMPORTER_PLUGIN_DIR', untrailingslashit(dirname(MAGENTO_IMPORTER_PLUGIN)));

class MagentoImporter
{

    protected $importers = [
        'articles-importer' => 'ArticlesImporter',
        'recipes-importer' => 'RecipesImporter'
    ];

    public function __construct()
    {
        if ($this->isWpCliRunning()) {
            $this->addCommands();
        }
    }

    protected function isWpCliRunning()
    {
        return defined('WP_CLI') && WP_CLI;
    }

    protected function addCommands()
    {
        foreach ($this->importers as $importerFile => $importerClass) {
            $filePath = MAGENTO_IMPORTER_PLUGIN_DIR . '/includes/' . $importerFile . '.php';
            if (is_file($filePath)) {
                require_once $filePath;
                WP_CLI::add_command($importerFile, $importerClass);
            }
        }
    }

}

new MagentoImporter();
