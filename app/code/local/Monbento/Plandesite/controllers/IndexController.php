<?php

class Monbento_Plandesite_IndexController extends Mage_Core_Controller_Front_Action
{

	  public function indexAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('plandesite.container')->setTitle('Plan du site');
		    $this->renderLayout();
	  }

}