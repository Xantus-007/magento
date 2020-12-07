<?php

class Dbm_Share_Controller_Upload extends Mage_Adminhtml_Controller_Action
{
    protected function _manageUpload($postName, $paramName, &$params, $offset = 0)
    {
        $result = false;
        $savePath =  Mage::helper('dbm_share')->getPhotoDir();

        if((isset($_FILES[$postName])
            && $_FILES[$postName]['name']
            && file_exists($_FILES[$postName]['tmp_name']))
            && !isset($params[$postName]['delete']))
        {
            $uploader = new Varien_File_Uploader($postName);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif', 'svg'));

            $filename = $_FILES[$postName]['name'];
            $newFileName = Mage::helper('dbm_share')->generateRandomFilename($filename, $offset);
            
            $params[$paramName]['value'] = basename($newFileName);

            $savePath = $this->_getUploadedSavePath($newFileName);

            if($uploader->save($savePath, $newFileName))
            {
                $result = true;
            }
        }
        elseif(isset($params[$postName]['delete']) && $params[$postName]['delete'] == 1)
        {
            @unlink($savePath.$params[$postName]['value']);

            //$params[$postName] = '';
        }

        return $result;
    }
}