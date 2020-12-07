<?php
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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Quadra
 * @package    Quadra_Atos
 * @name        Quadra_Atos_IController
 * @author      Quadra Team
 */

class Quadra_Atos_IController extends Mage_Core_Controller_Front_Action
{	
    public function mAction()
    {

        $file = Mage::getDesign()->getSkinUrl() . DS . 'images' . DS . 'media' . DS . 'atos' . DS . $this->getRequest()->getParam('g');
        $file = str_replace(Mage::getBaseUrl(), Mage::getBaseDir().'/', $file);
        if (!file_exists($file)) {
            die;
        } 

        /* Detect mime content type */
        if (function_exists('mime_content_type'))
            $mimeType = @mime_content_type($file);
        else
            $mimeType = 'image/gif';

        /* Set headers for download */
        header('Content-Type: ' . $mimeType);
        //ob_end_flush();
        $fp = fopen($file, 'rb');
        while (!feof($fp))
            echo fgets($fp, 16384);

        exit;
    }
}
