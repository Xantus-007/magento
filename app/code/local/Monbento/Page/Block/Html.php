<?php
class Monbento_Mage_Page_Block_Html extends Mage_Page_Block_Html
{
	public function getFeaturedProductHtml()
	{
		return $this->getBlockHtml('product_featured');
	}
	public function getFooterProductHtml()
	{
		return $this->getBlockHtml('product_footer');
	}
}
?>