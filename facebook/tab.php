<?php 
include_once 'config.php' ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head>
	<meta http-equiv="Content-language" content="fr" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Concours <?php echo NAME ; ?></title>
	<link rel="stylesheet" href="<?php echo BASE ; ?>css/global.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
	<script type="text/javascript" src="<?php echo $bHttps ? 'https' : 'http' ; ?>://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo BASE ; ?>js/main.js?v=<?php echo VERSION ; ?>"></script>
	<script src="<?php echo $bHttps ? 'https' : 'http' ; ?>://connect.facebook.net/<?php echo $locale ;?>/all.js"></script>
	<link href="<?php echo BASE ; ?>gfx/favicon.ico" rel="icon" type="image/x-icon" />
	<style>
	/*body
	{
		background:url("<?php echo $config->image_background ; ?>") no-repeat;
	}*/
	
	.fan_game h2, .fan_bloc b, .kids_list > a:hover, .kids_list > a.selected, .kids h2.big
	{
		color:#<?php echo $config->color11 ; ?>;
	}
	
	.button, .button_big
	{
		background:#<?php echo $config->color11 ; ?>;
	}
	
	#content, .fan_bloc
	{
		border-color:#<?php echo $config->color11 ; ?>;
	}
	</style>
</head>
<body>
	<div id="content">
		<?php 
		$page = '' ;
		$height = 1110 ;
		
		$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ; 
		$lot = $db->select($sql) ;
			
		if($lot && $lot->id_lot && $app_data != 'fin')
		{
			$aExcludesCountry = explode(',', $lot->exclude_countries) ;
			
			if(!in_array(strtoupper($country), $aExcludesCountry) && $app_data != 'strangers')
			{
				if($fid)
				{
					if($app_data == 'option_kids')
					{
						include_once 'pages/kids.php' ;
						$page = 'kids' ;
					}else{
						include_once 'pages/fan.php' ;
						$page = 'fan' ;
					}
				}else{
					include_once 'pages/nofan.php' ;
					$page = 'nofan' ;
				}
			}else{
				include_once 'pages/strangers.php' ;
				$page = 'strangers' ;
			}
		}else{
			include_once 'pages/end.php' ;
			$page = 'end' ;
		}
		?>
	</div>
	<div id="smoke" style="height:654px;"></div>
	<div id="popup"></div>
<div id="fb-root"></div>
<div id="clipboard"></div>
<script type="text/javascript">
  FB.init({appId: '<?php echo FB_ID ?>', status: true, cookie: true, xfbml: true, frictionlessRequests : true, channelUrl: '<?php echo BASE ; ?>channel.php'});
  FB.Canvas.setSize({ width: 810, height: $('#content').height() + 100 }) ;
  //FB.Canvas.setAutoGrow() ;
  
  FB.Event.subscribe('edge.create', function(response) 
  {
  		window.open('<?php echo FB_URL_TAB ; ?>&app_data=<?php echo $app_data == 'affi' ? 'affi_ok' : $app_data ; ?>', '_top') ;
  });
  
  bFan = <?php echo $bFan ? 1 : 0 ; ?> ;
  id_user = <?php echo $fid ; ?> ;
  var site = '<?php echo BASE ; ?>' ;
  var sName = '<?php echo addslashes(NAME) ; ?>' ;
  var url_tab = '<?php echo FB_URL_TAB ; ?>' ;
  var signed_request = '<?php echo $signed ; ?>' ;
  parrain = '<?php echo $app_data && substr($app_data, 0, 4) == 'pid_' ? substr($app_data, 4) : 0 ?>' ;
  var text_contest = '<?php echo addslashes(replace_tags($config->text_contest)) ; ?>' ;
  var text_publish_win_title = '<?php echo addslashes(replace_tags($config->text_win_publish_title, $lot)) ; ?>' ;
  var text_publish_win_desc = '<?php echo addslashes(replace_tags($config->text_win_publish_desc, $lot)) ; ?>' ;
  var text_invite_title = '<?php echo addslashes(replace_tags($config->text_invite_title, $lot)) ; ?>' ;
  var text_invite_msg = '<?php echo addslashes(replace_tags($config->text_invite_message, $lot)) ; ?>' ;
  var text_invite_alert = '<?php echo addslashes(replace_tags($config->text_invite_done, $lot)) ; ?>' ;
  var text_invite_alert2 = '<?php echo addslashes(replace_tags($config->text_invite_done2, $lot)) ; ?>' ;
  var text_optins_alert = '<?php echo addslashes(replace_tags($config->text_optins_alert, $lot)) ; ?>' ;
  var text_email_saved = '<?php echo addslashes(replace_tags($config->text_email_saved, $lot)) ; ?>' ;
  var share_img = '<?php echo BASE . 'lots/' . (substr($locale, 0, 2) == 'fr' ? $lot->image_fr : $lot->image_en) ; ?>' ;
</script>
<?php if(GA_UID) {?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo GA_UID ; ?>']);
  _gaq.push(['_trackPageview', '<?php echo $page ; ?>']);
  <?php if($app_data == 'notif') echo "_gaq.push(['_setCustomVar', 1, 'source', 'notif', 2]);" ;?>
  
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php } ?>
</body>
</html>