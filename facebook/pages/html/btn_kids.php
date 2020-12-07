<?php if($lot->option_kids && $user) { ?>
	<?php 
	$avis = $db->select('SELECT * FROM ' . DB_PREFIX . 'users_kidsavis WHERE id_user = "' . mysql_escape_string($user->id_user) . '"') ;
	
	if(!$avis) {
	?>	
	<div class="option_kids option_kids_discover" <?php isset($bHideKids) ? 'style="display:none;"' : '' ; ?>>
	<a href="javascript:void(0);" onclick="showKidsOption(); return false;" class="button"><?php echo $config->text_kids_btngo ; ?></a>
	</div>
	<?php }else{ ?>
	<div class="option_kids option_kids_exit">
	<a href="http://www.monbento.com/kids/index<?php echo substr($locale, 0, 2) == 'fr' ? '' : '.en' ; ?>.php?utm_campaign=concourskids&utm_source=facebook-application&utm_medium=socialmedia" target="_blank" class="button"><?php echo $config->text_kids_btnproducts ; ?></a>
	</div>
	<?php } ?>
<?php } ?>