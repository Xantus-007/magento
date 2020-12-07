<?php
$_SERVER['HTTPS'] = 'on';
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '5.3.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.3.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

ini_set('session.gc_maxlifetime', 1728000);

//error_reporting(E_ALL | E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE ^ E_DEPRECATED ^ E_STRICT ^ E_WARNING);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';
$isDbm = ($_SERVER['REMOTE_ADDR'] == '37.97.83.105' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '89.92.217.71');
$version = '';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

if (!$isDbm && file_exists($maintenanceFile)) {
    $basePath = dirname($_SERVER['PHP_SELF']);
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE']) || $isDbm) {
    Mage::setIsDeveloperMode(true);
}

if($isDbm)
{
    ini_set('display_errors', 1);
}
umask(0);

/* Gestion des langues / vues par domaine */
switch($_SERVER['HTTP_HOST']) {
    case "www.monbento.com":
    case "monbento.local.com":
    case "fr.monbento.dbm-dev.com":
    case "fr.monbento-2016.dbm-dev.com":
    case "monbento.com":
    case "monbento-b2c-old.it-consultis.net":
        $version = "fr";
        break;
    case "en.monbento.com":
    case "en.monbento.local.com":
    case "en.monbento.dbm-dev.com":
    case "en.monbento-2016.dbm-dev.com":
        $version = "en";
        break;
    case "www.monbento.it":
    case "monbento.it":
    case "it.monbento.local.com":
    case "it.monbento.dbm-dev.com":        
    case "it.monbento-2016.dbm-dev.com":
        $version = "it";
        break;
    case "www.monbento.es":
    case "monbento.es":
    case "es.monbento.local.com":
    case "es.monbento.dbm-dev.com":
    case "es.monbento-2016.dbm-dev.com":
        $version = "es";
        break;
    case "www.monbento.de":
    case "monbento.de":
    case "de.monbento.local.com":
    case "de.monbento.dbm-dev.com":
    case "de.monbento-2016.dbm-dev.com":
        $version = "de";
        break;
    case "us.monbento.com":
    case "us.monbento.local.com":
    case "us.monbento.dbm-dev.com":
    case "us.monbento-2016.dbm-dev.com":
        $version = "us_en";;
        break;
    /*case "hk.monbento.com":
    case "hk.monbento.local.com":
    case "hk.monbento-2016.dbm-dev.com":
        $version = "hk_en";
        break;*/
    case "www.monbento.co.uk":
    case "monbento.co.uk":
    case "uk.monbento.local.com":
    case "uk.monbento.dbm-dev.com":
    case "uk.monbento-2016.dbm-dev.com":
        case "monbento.uk":
        $version = "uk_en";
        break;
    default:
        header('Status: 301');
        header('Location: http://www.monbento.com/');
        exit;
}

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : $version;

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::run($mageRunCode, $mageRunType);

