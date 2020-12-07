<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;

$token 		= array_key_exists('token', $_POST) && $_POST['token'] ? $_POST['token'] : '' ;
$dataSigned = array_key_exists('signed_request', $_POST) && $_POST['signed_request'] ? parse_signed_request($_POST['signed_request'], FB_SECRET) : null ;
$id_user	= array_key_exists('id_user', $_POST) ? $_POST['id_user'] : 0 ;
$wall		= array_key_exists('wall', $_POST) ? (int) $_POST['wall'] : 0 ;
$ok			= false ;
$duo		= false ;
$bNew		= false ;
$friends	= array() ;

if($id_user)
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($id_user) . '"' ;
	$row 	= $db->select($sql) ;
	
	include_once $path . '/lib/facebook/facebook.php' ;
	
	if(!$dataSigned) unset($_REQUEST['signed_request']) ;
	$fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;
	
	if(!$row || !array_key_exists('id_user', $row))
	{
		try
		{
			$params = array('access_token' => $token ? $token : $dataSigned['oauth_token']) ;
			
			$dataFb = $fb->api($id_user, $params) ;
			
			$data = array() ;
			$data['id_user'] 	= $id_user ;
			$data['name'] 		= $dataFb['last_name'] ;
			$data['fname'] 		= $dataFb['first_name'] ;
			$data['email'] 		= $dataFb['email'] ;
			$data['sexe'] 		= $dataFb['gender'] ;
			$data['birthdate'] 	= array_key_exists('birthday', $dataFb) ? getDateFromFaceBook($dataFb['birthday']) : '' ;
			$data['city'] 		= array_key_exists('location', $dataFb) ? $dataFb['location']['name'] : '' ;
			$data['country'] 	= $country ;
			$data['locale'] 	= $locale ;
			$data['optin1'] 	= $_POST['optin1'] ;
			$data['optin2'] 	= $_POST['optin2'] ;
			$data['bitly'] 		= get_bitly_short_url(BASE . 'link.php?fid=' . $id_user . '&id_lot=' . $lot->id_lot) ;
			$data['date'] 		= date('Y-m-d H:i:s') ;
			
			$db->insert(DB_PREFIX . 'users', $data) ;
			$bNew = true ;
		}catch (Exception $e) {catch_error($e);}
	}else{
		$data = array() ;
		
		if(array_key_exists('optin1', $_POST) && $_POST['optin1'] == 1)
		{
			$data['optin1'] = 1 ;
		}
		
		if(array_key_exists('optin2', $_POST) && $_POST['optin2'] == 1)
		{
			$data['optin2'] = 1 ;
		}
		
		if($row && !$row->bitly)
		{
			$data['bitly'] = get_bitly_short_url(BASE . 'link.php?fid=' . $id_user. '&id_lot=' . $lot->id_lot) ;
		}
		
		if($row && $row->locale != $locale)
		{
			$data['locale'] = $locale ;
		}
		
		if(count($data))
		{
			$db->update(DB_PREFIX . 'users', $data, 'id_user = "' . mysql_escape_string($id_user) . '"') ;
		}
	}
	
	try
	{
		$params = array('access_token' => $token ? $token : $dataSigned['oauth_token']) ;
		$params['q'] 	= 'SELECT uid, name, first_name FROM user WHERE sex = "male" AND uid IN (SELECT uid1 FROM friend WHERE uid2=me())' ;
		
		$r = $fb->api('fql', $params) ;
		
		$friends = $r['data'] ;
	}catch (Exception $e) {
		catch_error($e);
	}
}

$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ; 
$lot = $db->select($sql) ;

$user = null ;
$bCanPlay = false ;
$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '' ;

if($id_user && $lot->id_lot)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($id_user) . '"' ; 
	$user = $db->select($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_ignored WHERE id_user = "' . mysql_escape_string($id_user) . '"' ; 
	$ignore = $db->select($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' AND id_user = "' . mysql_escape_string($id_user) . '"' ; 
	$alreadywon = $db->select($sql) ;
	
	$bCanPlay = $user && !$ignore ;
}

if($bCanPlay)
{
	$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_user = "' . mysql_escape_string($id_user) . '"' ;
	$row = $db->select($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY id_winner DESC LIMIT 0,1' ; 
	$winner = $db->select($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'participations WHERE id_user = "' . mysql_escape_string($id_user) . '" AND SUBSTRING(date, 1, 10) = SUBSTRING(NOW(), 1, 10)' ;
	$row = $db->select($sql) ;
	
	if(!$row)
	{
		// Check parrain
		$id_inv = 0 ;
		
		$pid = array_key_exists('pid', $_POST) ? $_POST['pid'] : 0 ;
		if($pid)
		{
			$id_inv =  mysql_escape_string($pid) ;
		}else{
			$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_invitations_clics c WHERE c.to = "' . mysql_escape_string($id_user) . '"' ; 
			if($winner && $winner->id_winner)
			{
				$sql.= ' AND date > "' . $winner->date . '"' ;
			}else{
				$sql.= ' AND date > "' . $lot->date_start . '"' ;
			}
			
			$sql.= ' ORDER BY id_clic DESC LIMIT 0,1' ;
			$parrain = $db->select($sql) ;
		
			if($parrain && $parrain->from)
			{
				$id_inv = $parrain->from ;
			}else{
				if(array_key_exists('id_inv', $_COOKIE))
				{
					$id_inv = $_COOKIE['id_inv'] ;
				}
			}
		}
		
		$data = array() ;
		$data['id_user']	= $id_user ;
		if($id_inv) $data['id_inv']	= $id_inv ;
		$data['id_lot']		= $lot->id_lot ;
		$data['date']		= date('Y-m-d H:i:s') ;
		$data['ip']			= $ip ;
		$data['sent']		= $wall ;
		$db->insert(DB_PREFIX . 'participations', $data) ;
		
		if(!$bNew)
		{
			$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_user = "' . mysql_escape_string($id_user) . '" AND id_lot = ' . $lot->id_lot ;
			$row = $db->select($sql) ;
			if($row->nb == 1)
			{
				$db->query('UPDATE ' . DB_PREFIX . 'users SET sent = NULL WHERE id_user = "' . mysql_escape_string($id_user) . '"') ;
			}
		}
		
		$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_lot = ' . $lot->id_lot ;
		if($winner && $winner->id_winner)
		{
			$sql.= ' AND date > "' . $winner->date . '"' ;
		}
		
		$row = $db->select($sql) ;
		$nb = $row->nb ;
		if(!$alreadywon && $user->email && $nb >= $lot->frequency) // Win
		{
			$data = array() ;
			$data['id_user']	= $id_user ;
			$data['id_lot']		= $lot->id_lot ;
			$data['date']		= date('Y-m-d H:i:s') ;
			$db->insert(DB_PREFIX . 'winners', $data) ;
			
			$nbWin = 1 ;
			
			if($id_inv && $lot->win + $nbWin < $lot->nb)
			{
				$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_ignored WHERE id_user = ' . $id_inv ; 
				$ignore = $db->select($sql) ;
				
				$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' AND id_user = ' . $id_inv ; 
				$alreadywon = $db->select($sql) ;
				
				if(!$ignore && !$alreadywon)
				{
					$data = array() ;
					$data['id_user']	= $id_inv ;
					$data['id_lot']		= $lot->id_lot ;
					$data['date']		= date('Y-m-d H:i:s') ;
					$db->insert(DB_PREFIX . 'winners', $data) ;
					
					$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $id_inv ; 
					$duo = $db->select($sql) ;
					
					$nbWin = 2 ;
				}
			}
			
			$sql = 'UPDATE ' . DB_PREFIX . 'lots SET win = win + ' . $nbWin . ' WHERE id_lot = ' . $lot->id_lot ;
			$db->query($sql) ;
			
			if($lot->win + $nbWin >= $lot->nb)
			{
				// Stop contest
				$sql = 'UPDATE ' . DB_PREFIX . 'lots SET state = 0, date_end = NOW() WHERE id_lot = ' . $lot->id_lot ;
				$db->query($sql) ;
			}
			
			$ok = true ;
		}
		
		// Publish OpenGraph
		if(!$bNew)
		{
			//publishOpenGraph($lot->id_lot) ;
		}
		
		// Add Scenario
		if($lot->option_kids)
		{
			$row = $db->select('SELECT * FROM ' . DB_PREFIX . 'users_scenarios_sends WHERE id_user = "' . mysql_escape_string($id_user) . '"') ;
			
			if(!$row)
			{
				$data = array() ;
				$data['id_user']	= $id_user ;
				$data['date']		= date('Y-m-d H:i:s', time() + 3600) ;
				$db->insert(DB_PREFIX . 'users_scenarios_sends', $data) ;
			}
		}
	}
}

// Winners
if($lot)
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY date DESC LIMIT 15' ;
	$winners = $db->selectAll($sql) ;
}

include_once 'html/game.php' ;
?>