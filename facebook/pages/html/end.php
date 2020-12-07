<div class="tab end">
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="end_game">
		<h2 class="big"><?php echo replace_tags($config->text_end_text1) ; ?></h2>
		<br/><br/>
		<h2><?php echo replace_tags($config->text_end_text2) ; ?></h2>
		<a class="button_big button_end" href="javascript:void(0);" onclick="save(); return false;" ><?php echo replace_tags($config->text_end_alert) ; ?></a>
	</div>
</div>
<?php 
	include_once 'footer.php' ;
?>