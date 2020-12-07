<?php 
$_POST['signed_request'] = $_GET['signed_request'] ;
include_once '../config.php' ;
?>
<a id="close" href="javascript:void(0)" onclick="closePopup();return false;"></a>
<div class="popup_content">
	<h2><?php echo $config->text_faq ; ?></h2>
	<br/><br/>
<?php 
echo $config->faq ;
?>
</div>