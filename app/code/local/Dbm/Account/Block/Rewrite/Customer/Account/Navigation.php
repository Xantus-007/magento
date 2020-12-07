<?php

class Dbm_Account_Block_Rewrite_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{

    protected $_links = array();
    protected $_activeLink = false;

    const ARG_ACCOUNT = 'account';
    const ARG_INDEX = 'index';
    const ARG_EDIT = 'edit';
    const ARG_LOGOUT = 'logout';

    public function addLink($name, $path, $label, $class = '', $urlParams = array())
    {
        $this->_links[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
            'class' => $class,
        ));
        return $this;
    }

    public function removeLinkByName($name)
    {
        unset($this->_links[$name]);
        return $this;
    }

    public function isActive($link)
    {
        if (empty($this->_activeLink))
        {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }

        $_completePath = $this->_completePath($link->getPath());
        $_activeLink = $this->_activeLink;

        $_isSubNavigation = $this->isSubNavigation($_completePath);


        if ($_completePath == $_activeLink || $_isSubNavigation)
        {
            return true;
        }

        return false;
    }

    private function isSubNavigation($link)
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();
        $argments = explode('/', $link);
        $controllerArgName = $argments[1];
        $actionArgName = $argments[2];

        $controllersName = array($controllerArgName, $controllerName);

        if (in_array(self::ARG_ACCOUNT, $controllersName) && (
                $actionArgName == self::ARG_INDEX && $actionName == self::ARG_EDIT ||
                $actionArgName == self::ARG_EDIT && $actionName == self::ARG_INDEX ||
                $actionArgName == self::ARG_LOGOUT
                ))
        {
            return false;
        }

        return $controllerArgName == $controllerName;
    }

}
