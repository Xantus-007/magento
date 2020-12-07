<?php
class Monbento_Bundle_ImageController extends Mage_Core_Controller_Front_Action
{

    public function generateAction()
    {
		if($this->getRequest()->isPost())
		{
			$customImage = Mage::helper('monbento_bundle')->getCustomImage($this->getRequest()->getParams());
			echo $customImage['url'];
		}
    }

    public function downloadAction()
    {
		if($this->getRequest()->isGet())
		{
			$params = $this->getRequest()->getParams();
			$customImage = Mage::helper('monbento_bundle')->getCustomImageFromImageIds($params);
			header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
			header("Pragma: public");
			header('Content-disposition: attachment; filename='.md5(serialize($params)).'.png');
			header("Content-type: 'image/png'");
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			ob_clean();
			flush();
			readfile($customImage['url']);
			unlink($customImage['file']);
		}
    }

}
