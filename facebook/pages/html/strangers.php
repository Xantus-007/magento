<div class="tab end">
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="end_game">
		<h2 class="big" style="font-size:25pt;line-height:30pt;"><?php echo replace_tags($config->text_strangers_text1) ; ?></h2>
		<br/><br/>
		<h2><?php echo replace_tags($config->text_strangers_text2) ; ?></h2>
	</div>
</div>
<?php 
	include_once 'footer.php' ;
?>