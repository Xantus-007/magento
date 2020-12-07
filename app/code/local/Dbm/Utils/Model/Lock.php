<?php

class Dbm_Utils_Model_Lock extends Mage_Core_Model_Abstract
{
    const ERROR_IMPORT_FOLDER = 'Import folder does not exist.';

    protected $_lockFolder;

    public function init($lockFolder)
    {
        $this->_lockFolder = Mage::getBaseDir('var') . DS . $lockFolder.DS;
        $this->_checkLockFolder();

        return $this;
    }

    public function lock($id)
    {
        $fileName = $this->_getLockFileName($id);
        touch($fileName);

        return $this;
    }

    public function unlock($id)
    {
        $fileName = $this->_getLockFileName($id);
        if(is_file($fileName))
        {
            @unlink($fileName);
        }

        return $this;
    }

    public function isLocked($id)
    {
        $fileName = $this->_getLockFileName($id);
        return file_exists($fileName);
    }

    protected function _getLockFileName($id)
    {
        $this->_checkLockFolder();
        return $this->_lockFolder.$id.'.lock';
    }

    protected function _checkLockFolder()
    {
        if(!is_dir($this->_lockFolder))
        {
            throw new Mage_Core_Exception(self::ERROR_IMPORT_FOLDER);
        }
    }
}