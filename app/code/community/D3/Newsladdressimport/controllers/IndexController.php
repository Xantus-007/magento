<?php
class D3_Newsladdressimport_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/newsladdressimport?id=15 
    	 *  or
    	 * http://site.com/newsladdressimport/id/15 	
    	 */
    	/* 
		$newsladdressimport_id = $this->getRequest()->getParam('id');

  		if($newsladdressimport_id != null && $newsladdressimport_id != '')	{
			$newsladdressimport = Mage::getModel('newsladdressimport/newsladdressimport')->load($newsladdressimport_id)->getData();
		} else {
			$newsladdressimport = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($newsladdressimport == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$newsladdressimportTable = $resource->getTableName('newsladdressimport');
			
			$select = $read->select()
			   ->from($newsladdressimportTable,array('newsladdressimport_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$newsladdressimport = $read->fetchRow($select);
		}
		Mage::register('newsladdressimport', $newsladdressimport);
		*/

			
		/*$this->loadLayout();     
		$this->renderLayout();
		*/
		
		$this->loadLayout(); 
		$this->_setActiveMenu('newsladdressimport/items');
	
		$this->_addContent($this->getLayout()->createBlock('newsladdressimport/admin/html_newsladdressimport_grid'));
		$this->renderLayout();   
    }
}