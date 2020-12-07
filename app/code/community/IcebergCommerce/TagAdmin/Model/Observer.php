<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_TagAdmin
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_TagAdmin_Model_Observer 
{
	/**
	 * Prep is minimal. No checking is really required.
	 * 
	 * @param unknown_type $observer
	 */
	public function prepareSaveTags( $observer ){		
		$tagName = $observer->getEvent()->getRequest()->getPost('tagAdminSave');
		$observer->getEvent()->getProduct()->setSaveTagData( $tagName );
		
	}
	/**
	 * Saving Attribute.
	 * 
	 * @param unknown_type $observer
	 */
	public function saveTags( $observer )
	{
		$tagName = $observer->getEvent()->getProduct()->getSaveTagData();
		
		$this->saveTag( $tagName ,  $observer->getEvent()->getProduct() );
	}
	
	
	/**
	 * Add product tags to database.
	 * 
	 * @param string $tagName
	 * @param Mage_Catalog_Product $product
	 */
	public function saveTag( $tagName , $product )
	{
        $model = Mage::getModel('tag/tag');
        
        $stores = array( $product->getStoreId() );
        
        //Get the current store views.
        if( !$product->getStoreId() ) {
        	$stores = $product->getStoreIds();
        }
        

        if(strlen($tagName) ) {

            if(!$product->getId()){
				Mage::throwException(Mage::helper('tagadmin')->__('Product Error'));
            } else {
                
                $productId = $product->getId();
            	
                try {
                    $tagNamesArr = explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagName));

                    //Clean Arguments
                    foreach( $tagNamesArr as $key => $tagName ) {
                        $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
                        $tagNamesArr[$key] = trim($tagNamesArr[$key]);
                        if( $tagNamesArr[$key] == '' ) {
                            unset($tagNamesArr[$key]);
                        }
                    }
                    $newCount = 0;

                    foreach( $tagNamesArr as $tagName ) {
                        if( $tagName ) {
                        	$isNew = false;
                        	
                            $tagModel = Mage::getModel('tag/tag');

							//Mage::register('current_tag', $model);
							$tagModel->loadByName( $tagName );
							
                            if (!$tagModel->getId()) {
								// Tag does not exist. Create it.
                                $tagModel = Mage::getModel('tag/tag');
                                $tagModel->setStoreId( $stores[0] );
                                $tagModel->setFirstStoreId( $stores[0] );
                                
                                $tagModel->addData( array( 
	                            	'name'   => $tagName,
	                            	'status' => $tagModel->getApprovedStatus(),
                                	'store'  => $stores[0],
                                	'base_popularity' => 0
	                            ));
	                     
	                            $tagModel->save();
	                            //$tagModel->aggregate();
	                            
	                            $isNew = true;
                            }else{
                            	if( $tagModel->getStatus() != $tagModel->getApprovedStatus() )
                            	{
	                            	$tagModel->setStatus( $tagModel->getApprovedStatus() );
	                            	$tagModel->save();
                            	}
                            	//$tagModel->aggregate();
                            }
                           
                            foreach( $stores as $store )
                            {
                            	$tagRelationModel = Mage::getModel('tag/tag_relation');
                            	$tagModel->setStoreId( $store );
                            	$tagModel->setStore( $store );
                            	
                            	$products = $tagModel->getRelatedProductIds();
                            	
                            	if( !in_array( $productId , $products ) )
                            		$products[] = $productId;
                            	
                            	$tagRelationModel->addRelations($tagModel , $products );
                            	unset( $tagRelationModel );
                            }
                            
                            $tagModel->save();
                            $tagModel->aggregate();
                            
                            Mage::getSingleton('adminhtml/session')->setTagData(false);
                            
                            unset( $tagModel);
                        } else {
                            continue;
                        }
                    }
                } catch (Exception $e) {
                	Mage::throwException(Mage::helper('tag')->__('Unable to save tag(s)') . $e->getMessage() );
                }
            }
        }
    }
}