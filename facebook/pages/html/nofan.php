<div class="tab nofan">
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="image_nonfan">
		<img src="<?php echo $config->image_nonfan ; ?>" />
	</div>
	<div class="nofan_like">
		<h2><em><?php echo replace_tags($config->text_nofan_text1) ; ?></em></h2><br>
		<h2><b><?php echo replace_tags($config->text_nofan_text2) ; ?></b></h2>
		<a href="javascript:void(0);" onclick="launch(); return false;" class="button button_play"><?php echo $config->text_nofan_play ; ?></a>
		<div class="facepile">
			<div class="fb-facepile" data-href="<?php echo FB_URL_PAGE ; ?>" data-size="large" data-max-rows="1" data-width="500"></div>
		</div>
	</div>
</div>
<?php 
	include_once 'winners.php' ;
	include_once 'footer.php' ;
?>