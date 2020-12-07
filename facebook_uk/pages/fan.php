<?php
$bCanPlay = true ;
if($fid)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY id_winner DESC LIMIT 0,1' ; 
	$result = mysql_query($sql);
	$winner = mysql_fetch_object($result) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'participations WHERE id_user = ' . $fid . ' AND id_lot = ' . $lot->id_lot ;
	if($winner && $winner->id_winner)
	{
		$sql.= ' AND date > "' . $winner->date . '"' ;
	}
	$result = mysql_query($sql);
	$row = mysql_fetch_object($result) ;
	
	$bCanPlay = $row ? false : true ;
}
?>
<table cellpadding="0" cellspacing="0" background="gfx/tab_game.jpg" width="520" height="670">
	<tr>
		<td style="text-align:center;height:640px;">
			<table width="100%;" cellpadding="0" cellspacing="0" style="margin-top:20px;">
				<tr>
					<td style="font-size:36px;padding-left:30px;width:285px;line-height: 42px;">
						To win :<br/>
						1 <?php echo stripslashes($lot->name) ; ?>
					</td>
					<td style="padding-right:25px;text-align:right;">
						<img src="lots/<?php echo $lot->id_lot ; ?>.jpg" />
					</td>
				</tr>
				<?php 
				if($bCanPlay)
				{
				?>
				<tr>
					<td colspan="2" style="font-size:22px;text-align:center;padding:25px 30px 30px;">
						To play click on the button below. If you’re the <?php echo $lot->frequency ; ?>th player, you win !
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;padding:15px 30px 10px;">
						<div id="notice">
							<a href="javascript:void(0);" onclick="answer(); return false;" class="button" style="margin-left:100px;font-size:18px;">Try your luck</a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:0px 50px 30px;">
						<input type="hidden" value="0" id="notif" /><label for="notif" class="checkbox">Alert me when other prizes are brought into play</label><br/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:0px 50px 5px;">
						<input type="hidden" value="1" id="wall"/><label for="wall" class="checkbox checkbox_checked">Publish my participation Facebook</label>
					</td>
				</tr>
				<?php }else{ ?>
				<tr>
					<td colspan="2" style="font-size:22px;text-align:center;padding:25px 30px 30px;">
						You have already played. Invite your friends to win a prize.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;padding:15px 30px 30px;">
						<div id="notice">
							<a href="javascript:void(0);" onclick="inviteFriends('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button" style="margin-left:100px;">Invite your friends to win 1 <?php echo $lot->name ; ?></a>
						</div>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2" style="padding:0 10px;">
						<?php 
						$sql 	= 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'winners' ;
						$result = mysql_query($sql) ;
						$row = mysql_fetch_array($result) ;
						if($row['nb'])
						{					
						?>
						<table cellpadding="0" cellspacing="0" width="100%" style="border-top:1px dotted #c31681;">
							<tr>
								<td style="color:#434242;font-size:22px;text-align:center;padding-top:20px;">They won the last prizes</td>
							</tr>
							<tr>
								<td style="padding:20px 50px 0;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<?php 
												$sql 	= 'SELECT w.*, u.fname, l.name FROM ' . DB_PREFIX . 'winners w LEFT JOIN ' . DB_PREFIX . 'users u ON w.id_user = u.id_user LEFT JOIN ' . DB_PREFIX . 'lots l ON w.id_lot = l.id_lot ORDER BY id_winner DESC LIMIT 0,3' ;
												$result = mysql_query($sql) ;
												$i = 0 ;
												$aFields = array() ;
												while($row = mysql_fetch_array($result))
												{
													echo '<td>' ;
													echo '<img width="50" height="50" style="border:1px solid #ccc;margin-right:5px;" src="http://graph.facebook.com/' . $row['id_user'] . '/picture" />' ;
													echo '</td>' ;
													
													echo '<td ' . ($i < 2 ? 'style="padding-right:35px;"' : '') . '>' ;
													echo '<img width="50" height="50" style="border:1px solid #ccc;" src="' . BASE . 'lots/' . $row['id_lot'] . '.jpg" />' ;
													echo '</td>' ;
													
													array_push($aFields, $row['fname']) ;
													array_push($aFields, $row['name']) ;
													
													$i++ ;
												}
												echo '<tr>' ;
												foreach($aFields as $f)
												{
													echo '<td>' ;
													echo $f ;
													echo '</td>' ;
												}
												echo '</tr>' ;
											?>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php } ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
		include_once 'footer.php' ;
	?>
</table>
<?php 
	include_once 'disclaimer.php' ;
?>