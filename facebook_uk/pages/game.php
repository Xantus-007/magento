<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;
include_once $path . '/functions.php' ;

$token 		= array_key_exists('token', $_POST) && $_POST['token'] ? $_POST['token'] : '' ;
$dataSigned = array_key_exists('signed_request', $_POST) && $_POST['signed_request'] ? parse_signed_request($_POST['signed_request'], FB_SECRET) : null ;
$id_user	= array_key_exists('id_user', $_POST) ? (int) $_POST['id_user'] : 0 ;
$notif		= array_key_exists('notif', $_POST) ? (int) $_POST['notif'] : 0 ;
$wall		= array_key_exists('wall', $_POST) ? (int) $_POST['wall'] : 1 ;
$ok			= false ;
$duo		= false ;

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $id_user ;
$result = mysql_query($sql) ;
$row = mysql_fetch_array($result) ;

if(!$row || !array_key_exists('id_user', $row) || $row['birthdate'] == '0000-00-00')
{
	include_once $path . '/lib/facebook/facebook.php' ;

	$fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;
	
	try
	{
		$params = array('access_token' => $token ? $token : $dataSigned['oauth_token']) ;
		
		$dataFb = $fb->api($id_user, $params) ;
		
		$data = array() ;
		$data['birthdate'] 	= array_key_exists('birthday', $dataFb) ? getDateFromFaceBook($dataFb['birthday']) : '' ;
		
		if($row && array_key_exists('id_user', $row) && $row['id_user'])
		{
			$db->update(DB_PREFIX . 'users', $data, 'id_user = ' . $id_user) ;
		}else{
			$data['id_user'] 	= $id_user ;
			$data['name'] 		= $dataFb['last_name'] ;
			$data['fname'] 		= $dataFb['first_name'] ;
			$data['email'] 		= $dataFb['email'] ;
			$data['sexe'] 		= $dataFb['gender'] ;
			$data['city'] 		= array_key_exists('location', $dataFb) ? $dataFb['location']['name'] : '' ;
			$data['notif'] 		= $notif ;
			$data['date'] 		= date('Y-m-d H:i:s') ;
			
			$db->insert(DB_PREFIX . 'users', $data) ;
		}
	}catch (Exception $e) {/*var_dump($e);*/}
}

$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ; 
$result = mysql_query($sql);
$lot = mysql_fetch_object($result) ;

$bCanPlay = false ;
if($id_user && $lot)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $id_user ; 
	$result = mysql_query($sql);
	$valid = mysql_fetch_object($result) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_ignored WHERE id_user = ' . $id_user ; 
	$result = mysql_query($sql);
	$ignore = mysql_fetch_object($result) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' AND id_user = ' . $id_user ; 
	$result = mysql_query($sql) ;
	$alreadywon = mysql_fetch_object($result) ;
	
	$bCanPlay = $valid && !$ignore && !$alreadywon ;
}

if($bCanPlay)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY id_winner DESC LIMIT 0,1' ; 
	$result = mysql_query($sql);
	$winner = mysql_fetch_object($result) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'participations WHERE id_user = ' . $id_user . ' AND id_lot = ' . $lot->id_lot ;
	if($winner && $winner->id_winner)
	{
		$sql.= ' AND date > "' . $winner->date . '"' ;
	}
	$result = mysql_query($sql);
	$row = mysql_fetch_object($result) ;
	
	if(!$row)
	{
		$data = array() ;
		$data['id_user']	= $id_user ;
		$data['id_lot']		= $lot->id_lot ;
		$data['date']		= date('Y-m-d H:i:s') ;
		if(!$wall) $data['sent']	= 1 ;
		$db->insert(DB_PREFIX . 'participations', $data) ;
		
		$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_lot = ' . $lot->id_lot ;
		if($winner && $winner->id_winner)
		{
			$sql.= ' AND date > "' . $winner->date . '"' ;
		}
		
		$result = mysql_query($sql);
		$row = mysql_fetch_object($result) ;
		$nb = $row->nb ;
		if($nb == $lot->frequency) // Win
		{
			$data = array() ;
			$data['id_user']	= $id_user ;
			$data['id_lot']		= $lot->id_lot ;
			$data['date']		= date('Y-m-d H:i:s') ;
			$db->insert(DB_PREFIX . 'winners', $data) ;
			
			$nbWin = 1 ;
			
			// Clean publications for the day
			$sql = 'UPDATE ' . DB_PREFIX . 'publications SET sent = 1 WHERE sent IS NULL AND date = "' . date('Y-m-d') . '"' ;
			mysql_query($sql) ;
			
			// Check parrain
			$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_invitations_clics c WHERE c.to = ' . $id_user ; 
			if($winner && $winner->id_winner)
			{
				$sql.= ' AND date > "' . $winner->date . '"' ;
			}else{
				$sql.= ' AND date > "' . $lot->date_start . '"' ;
			}
			
			$sql.= ' ORDER BY id_clic DESC LIMIT 0,1' ;
			$result = mysql_query($sql);
			$parrain = mysql_fetch_object($result) ;
			
			if($parrain && $parrain->from)
			{
				$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_ignored WHERE id_user = ' . $parrain->from ; 
				$result = mysql_query($sql) ;
				$ignore = mysql_fetch_object($result) ;
				
				$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' AND id_user = ' . $parrain->from ; 
				$result = mysql_query($sql) ;
				$alreadywon = mysql_fetch_object($result) ;
				
				if(!$ignore && !$alreadywon)
				{
					$data = array() ;
					$data['id_user']	= $parrain->from ;
					$data['id_lot']		= $lot->id_lot ;
					$data['date']		= date('Y-m-d H:i:s') ;
					$db->insert(DB_PREFIX . 'winners', $data) ;
					
					$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $parrain->from ; 
					$result = mysql_query($sql);
					$duo = mysql_fetch_object($result) ;
					
					$nbWin = 2 ;
				}
			}
			
			$sql = 'UPDATE ' . DB_PREFIX . 'lots SET win = win + ' . $nbWin . ' WHERE id_lot = ' . $lot->id_lot ;
			mysql_query($sql) ;
			
			// Add page publication
			$d = 2 + rand(0, 3) ;
			$sql = 'INSERT INTO ' . DB_PREFIX . 'publications VALUES ("", ' . $lot->id_lot . ', DATE_ADD(NOW(), INTERVAL ' . $d . ' DAY), ' . $id_user . ',' . ($duo ? $duo->id_user : 'NULL') . ', NULL);' ;
			mysql_query($sql) ;
			
			if($lot->win + $nbWin >= $lot->nb)
			{
				// Stop contest
				$sql = 'UPDATE ' . DB_PREFIX . 'lots SET state = 0, date_end = NOW() WHERE id_lot = ' . $lot->id_lot ;
				mysql_query($sql) ;
				
				// Clean publications for the lot
				$sql = 'UPDATE ' . DB_PREFIX . 'publications SET sent = 1 WHERE sent IS NULL AND id_lot = ' . $lot->id_lot ;
				mysql_query($sql) ;
			}
			
			$ok = true ;
		}
	}
}
?>
<table cellpadding="0" cellspacing="0" background="gfx/tab_game.jpg" width="520" height="670">
	<tr>
		<td height="645" style="text-align:center;">
<?php
if($ok)
{
?>
			<table width="100%;" cellpadding="0" cellspacing="0" style="margin-top:20px;">
				<tr>
					<td style="padding-left:30px;width:280px;">
						<span style="font-size:36px;">You won !</span><br/>
						<span style="font-size:22px;">
						<?php 
						if($duo)
						{
							echo 'Congrats, you won and ' . $duo->fname . ' ' . $duo->name . ' won thanks to you' ;
							echo 'Both of you will receive an email to get your prize back' ;
						}else{
							echo 'You will receive an email to get your prize back' ;
						}
						?>
						</span>
					</td>
					<td style="padding-right:25px;text-align:right;">
						<img src="lots/<?php echo $lot->id_lot ; ?>.jpg" />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;padding:50px 30px 20px;">
						<a href="javascript:void(0);" onclick="publishWin(<?php echo $lot->id_lot . ',' . $lot->nb . ', \'' . addslashes(stripslashes($lot->name)) . '\'' ; ?>); return false;" class="button" style="margin-left:100px;">Publish on your wall the prize you just won</a>
					</td>
				</tr>
				<?php 
				if($duo)
				{
				?>
				<tr>
					<td colspan="2" style="text-align:center;padding:10px 30px 20px;">
						<a href="javascript:void(0);" onclick="publishFriend(<?php echo $duo->id_user . ',' . $lot->id_lot . ',' . $lot->nb . ', \'' . addslashes(stripslashes($lot->name)) . '\'' ; ?>); return false;" class="button2" style="margin-left:100px;">Alert <?php echo $duo->fname ; ?> by publishing on his wall that he won thanks to you</a>
					</td>
				</tr>
				<?php }
				?>
				<tr>
					<td colspan="2" style="font-size:12px;padding:35px 30px 30px;">
						
					</td>
				</tr>
			</table>
<?php
}else{
?>
			<table width="100%;" cellpadding="0" cellspacing="0" style="margin-top:20px;">
				<tr>
					<td style="padding-left:30px;width:280px;">
						<span style="font-size:36px;">You lost but...</span><br/>
						<span style="font-size:22px;">you still can win :<br/>if one of your friends participates and wins thanks to you !</span>
					</td>
					<td style="padding-right:25px;text-align:right;">
						<img src="lots/<?php echo $lot->id_lot ; ?>.jpg" />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size:12px;padding:25px 30px 30px;">
						Who should I invite ?<br/>
						All of your friend can play, but try to invite first people who may like the prize : the ones who love cooking, trends, bento, Japan, Asiatic culture, manga...
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;padding:10px 30px 30px;">
						<div id="notice">
							<?php 
							$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_lot = ' . $lot->id_lot . ' AND id_user = ' . $id_user ; 
							$row = $db->select($sql) ;
							
							if($row->nb == 1)
							{
							?>
							<a id="inviteFriends" href="javascript:void(0);" onclick="inviteFriends('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button" style="margin-left:100px;">Invite your friends to win 1 <?php echo $lot->name ; ?></a>
							<?php }else{ ?>
							<a id="inviteFriends" href="javascript:void(0);" onclick="inviteFriends('<?php echo addslashes(stripslashes($lot->name)) ; ?>'); return false;" class="button" style="margin-left:100px;">Invite again your friends to win 1 <?php echo $lot->name ; ?></a>
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size:12px;padding:25px 30px 30px;">
						You can try again your luck when a new  <?php echo $lot->name ; ?> will be put in game
					</td>
				</tr>
			</table>
<?php
}
?>
		</td>
	</tr>
	<?php 
		include_once 'footer.php' ;
	?>
</table>
<?php 
	include_once 'disclaimer.php' ;
?>