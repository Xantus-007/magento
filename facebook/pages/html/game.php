<?php 
$prevInv = array() ;
/*$ok = 1 ;
$duo = true ;*/
?>
<div class="tab game <?php echo $ok ? 'game_win' : 'game_lost' ?>" style="height:700px;">
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="image_fan">
		<img src="<?php echo $config->image_fan ; ?>" />
	</div>
	<div class="fan_game">
<?php
if($ok)
{
?>
	<h2><?php echo replace_tags($config->text_win_title, $lot, $user) ; ?></h2>
	<?php
		echo replace_tags($config->text_win_subtitle1, $lot, $user) . '<br/><br/>' ;
		echo '<span class="lite">' . replace_tags($config->text_win_subtitle2, $lot, $user) . '</span><br/><br/>' ;
	?>
	<a href="javascript:void(0);" onclick="publishWin(<?php echo $lot->id_lot . ',' . $lot->nb . ', \'' . addslashes(stripslashes($lot->name_fr)) . '\'' ; ?>); return false;" class="button button_win"><?php echo $config->text_win_publish ; ?></a>
	<?php
	if($duo)
	{
	?>
	<table>
		<tr>
			<td style="vertical-align:top;padding-right:15px;">
				<img width="50" height="50" src="http://graph.facebook.com/<?php echo $duo->id_user ; ?>/picture" />
			</td>
			<td style="vertical-align:top;">
				<?php
					echo '<b>' . replace_tags($config->text_win_subtitle3, $lot, $duo) . '</b><br/>' ;
					echo replace_tags($config->text_win_subtitle4, $lot, $user) ;
				?>
			</td>
		</tr>
	</table>
	<?php } ?>
	<?php 
		include_once 'btn_kids.php' ;
	?>
<?php
}else{
// Previous Sends
$sql = 'SELECT DISTINCT(id_friend) id_friend FROM ' . DB_PREFIX . 'users_invitations_sends' ;
$sql.= ' WHERE id_user = "' . mysql_escape_string($id_user) . '" ORDER BY id_send DESC LIMIT 0,50' ;
$prevInv = $db->selectAll($sql) ;
?>
	<h2><?php echo replace_tags($config->text_lost_title, $lot, $user) ; ?></h2>
	<?php echo replace_tags($config->text_lost_text1) ; ?><br/>
	<?php echo replace_tags($config->text_lost_text2) ; ?><br/>
	<?php echo replace_tags($config->text_lost_text3) ; ?><br/><br/>
	<?php if(count($prevInv)) { ?>
		<a href="javascript:void(0);" onclick="inviteFriends('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button button_invite" style="margin-left:20px;"><?php echo $config->text_lost_invite ; ?></a>
		<a href="javascript:void(0);" onclick="inviteFriendsPrev('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button button_invite2"><?php echo $config->text_lost_invite2 ; ?></a>
	<?php }else{ ?>
		<a href="javascript:void(0);" onclick="inviteFriends('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button button_invite"><?php echo $config->text_lost_invite ; ?></a>
	<?php } ?>
	<br/><br/><br/><?php echo $config->text_lost_bitly ; ?><br/>
	<input type="text" id="link" value="<?php echo $user->bitly ; ?>" onclick="this.select();"/>
	<a href="javascript:void(0);" onclick="shareFriends('<?php echo $user->bitly ; ?>'); return false;" class="button button_share"><?php echo $config->text_lost_share ; ?></a>
	<a href="javascript:void(0);" onclick="sendFriends('<?php echo $user->bitly ; ?>'); return false;" class="button button_send"><?php echo $config->text_lost_send ; ?></a>
	<?php 
		$bHideKids = true ;
		include_once 'btn_kids.php' ;
	?>
<?php
}
?>
	</div>
</div>
<script>
sFname = '<?php echo $user ? addslashes(ucfirst($user->fname)) : '' ; ?>' ;
var bNew = <?php echo $bNew ? 1 : 0 ; ?> ;
<?php 
if(count($friends))
{
	$aFriends = array() ;
	$i = 0 ;
	foreach($friends as $f)
	{
		$uid = number_format($f['uid'], 0, '', '') ;
			
		array_push($aFriends, $uid) ;
		$i++ ;
		//if($i == 50) break ;
	}
	
	echo 'aFriendsMale = [' . implode(',', $aFriends) . '] ;' ;
}

// Previous Sends
if(count($prevInv))
{
	$aFriends = array() ;
	foreach($prevInv as $l)
	{
		array_push($aFriends, $l->id_friend) ;
	}
	
	echo 'aFriendsPrevious = [' . implode(',', $aFriends) . '] ;' ;
}
?>

if($('.button_invite2').length)
{
	$('.button_invite').css('margin-left', (415 - 20 - $('.button_invite').width() - $('.button_invite2').width()) / 2) ;
}else{
	$('.button_invite').css('margin-left', (475 - $('.button_invite').width()) / 2) ;
}
$('.button_send').css('margin-left', (475 - $('.button_send').outerWidth()) / 2) ;

trackPreview('/game') ;
</script>
<?php 
	include_once 'winners.php' ;
	include_once 'footer.php' ;
?>