<?php 
$bShowOptin1 = !$user || $user->optin1 == 0 ;
$bShowOptin2 = !$user || $user->optin2 == 0 ;
?>
<div class="tab fan" <?php echo !$bCanPlay ? 'style="height:700px;"' : '' ; ?>>
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="image_fan">
		<img src="<?php echo $config->image_fan ; ?>" />
	</div>
	<div class="fan_game">
		<?php if($bCanPlay) { ?>
		<h2><?php echo $config->text_fan_title ; ?></h2>
		<?php
		echo replace_tags($config->text_fan_subtitle1, $lot, $user) . '<br/>' ;
		echo replace_tags($config->text_fan_subtitle2, $lot, $user) . '<br/><br/>' ;
		
		$n = 1 ;
		?>
		<?php if($bShowOptin1) {?>
		<div id="fan_optin1" class="fan_bloc fan_bloc1">
			<input type="hidden" value="-1" name="optin1" id="optin1" />
			<span class="text text_none">
				<b><?php echo $n++ ; ?></b><?php echo $config->text_fan_optin1 ; ?><br/><br/>
				<a href="javascript:void(0);" onclick="updateState(1, 1); return false;" class="button"><?php echo $config->text_fan_optin1_ok ; ?></a>
				<a href="javascript:void(0);" onclick="updateState(1, 0); return false;" class="link"><?php echo $config->text_fan_optin1_no ; ?></a>
			</span>
			<span class="text text_yes"><?php echo $config->text_fan_optin1_done ; ?></span>
		</div>
		<?php }else{ ?>
		<input type="hidden" value="1" name="optin1" id="optin1" />
		<?php } ?>
		<?php if($bShowOptin2) {?>
		<div id="fan_optin2" class="fan_bloc fan_bloc1">
			<input type="hidden" value="-1" name="optin2" id="optin2" />
			<span class="text text_none">
				<b><?php echo $n++ ; ?></b><?php echo $config->text_fan_optin2 ; ?><br/><br/>
				<a href="javascript:void(0);" onclick="updateState(2, 1); return false;" class="button"><?php echo $config->text_fan_optin2_ok ; ?></a>
				<a href="javascript:void(0);" onclick="updateState(2, 0); return false;" class="link"><?php echo $config->text_fan_optin2_no ; ?></a>
			</span>
			<span class="text text_yes"><?php echo $config->text_fan_optin2_done ; ?></span>
		</div>
		<?php }else{ ?>
		<input type="hidden" value="1" name="optin2" id="optin2" />
		<?php } ?>
		<div class="fan_bloc fan_bloc2">
			<b><?php echo $n++ ; ?></b>
			<?php echo $config->text_fan_play_text ; ?><br/><br/><br/>
			<a href="javascript:void(0);" onclick="play(); return false;" class="button button_play"><?php echo $config->text_fan_play ; ?></a>
		</div>
		<?php }else{ ?>
		<h2><?php echo replace_tags($config->text_fan_played_title, $lot, $user) ; ?></h2>
		<?php echo replace_tags($config->text_fan_played_text1) ; ?><br/>
		<?php echo replace_tags($config->text_fan_played_text2) ; ?><br/><br/>
		<a href="javascript:void(0);" onclick="inviteFriends(); return false;" class="button button_invite"><?php echo $config->text_lost_invite ; ?></a>
		<br/><br/><br/><?php echo $config->text_lost_bitly ; ?><br/>
		<input type="text" id="link" value="<?php echo $user->bitly ; ?>" onclick="this.select();"/>
		<a href="javascript:void(0);" onclick="shareFriends('<?php echo $user->bitly ; ?>'); return false;" class="button button_share"><?php echo $config->text_lost_share ; ?></a>
		<a href="javascript:void(0);" onclick="sendFriends('<?php echo $user->bitly ; ?>'); return false;" class="button button_send"><?php echo $config->text_lost_send ; ?></a>
		<script>
		$('.button_invite').css('margin-left', (445 - $('.button_invite').width()) / 2) ;
		$('.button_send').css('margin-left', (445 - $('.button_send').width()) / 2) ;
		</script>
		<?php 
		include_once 'btn_kids.php' ;
		?>
		<?php } ?>
	</div>
</div>
<script>
$('.button_send').css('margin-left', (475 - $('.button_send').outerWidth()) / 2) ;
</script>
<?php 
	include_once 'winners.php' ;
	include_once 'footer.php' ;
?>