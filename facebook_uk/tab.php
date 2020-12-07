<?php 
include_once 'config.php' ;
include_once 'functions.php' ;

$dataSigned = !empty($_POST) ? parse_signed_request($_POST['signed_request'], FB_SECRET) : null ;
$bFan 		= $dataSigned ? $dataSigned['page']['liked'] : true ;
$fid 		= $dataSigned && array_key_exists('user_id', $dataSigned) ? $dataSigned['user_id'] : 0 ;
$signed		= !empty($_POST) ? $_POST['signed_request'] : '' ;

//$fid = 10 ;

//var_dump($dataSigned) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head>
	<meta http-equiv="Content-language" content="fr" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo NAME ; ?></title>
	<link rel="stylesheet" href="css/global.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/main.js?v=<?php echo VERSION ; ?>"></script>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
</head>
<body>
	<div id="content">
		<?php 
		$page = '' ;
		if($bFan)
		{
			$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ; 
			$lot = $db->select($sql) ;
			
			if($lot && $lot->id_lot)
			{
				include_once 'pages/fan.php' ;
			}else{
				include_once 'pages/end.php' ;
			}
		}else{
			include_once 'pages/nofan.php' ;
		}
		?>
	</div>
<div id="fb-root"></div>
<script type="text/javascript">
  FB.init({appId: '<?php echo FB_ID ?>', status: true, cookie: true, xfbml: true});
  FB.Canvas.setSize() ;
  //FB.Canvas.setAutoResize() ;
  
  FB.Event.subscribe('edge.create', function(response) 
  {
  		window.open('<?php echo FB_URL_TAB ; ?>&app_data=-1', '_top') ;
  });
  
  var bFan = <?php echo $bFan ? 1 : 0 ; ?> ;
  id_user = <?php echo $fid ; ?> ;
  var site = '<?php echo BASE ; ?>' ;
  var url_tab = '<?php echo FB_URL_TAB ; ?>' ;
  var signed_request = '<?php echo $signed ; ?>' ;
</script>
</body>
</html>