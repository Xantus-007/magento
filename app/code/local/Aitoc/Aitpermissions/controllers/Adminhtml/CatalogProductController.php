<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/controllers/Adminhtml/CatalogProductController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('11f933530dec7de9f97b47c93017e3de'); ?><?php
class Aitoc_Aitpermissions_Adminhtml_CatalogProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Update product(s) owners action
     *
     */
    public function massOwnerAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $owner      = (int)$this->getRequest()->getParam('created_by');

        try {
            if (version_compare(Mage::getVersion(), '1.4.2.0', '>=') && !Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception(
                    $this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
                );
            }
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('created_by' => $owner), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the product(s) owners.'));
        }

        $this->_redirect('adminhtml/catalog_product/', array('store'=> $storeId));
    }     
} } 