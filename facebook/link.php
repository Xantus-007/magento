<?php 
include_once 'config.php' ;
include_once 'functions.php' ;

$fid	= array_key_exists('fid', $_GET) ? $_GET['fid'] : 0 ;
$id_lot	= array_key_exists('id_lot', $_GET) ? (int) $_GET['id_lot'] : 0 ;

if($id_lot)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $id_lot ;
	$lot = $db->select($sql) ;
}else{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
	$lot = $db->select($sql) ;
}

$config_fr = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 1') ;
$config_uk = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 2') ;

$locale = array_key_exists('fb_locale', $_GET) ? $_GET['fb_locale'] : 'fr_FR' ;
if($fid)
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($fid) . '"' ;
	$user 	= $db->select($sql) ;
	if($user) $locale = $user->locale ;
}
$config = substr($locale, 0, 2) == 'fr' ? $config_fr : $config_uk ;

$url = BASE . 'link.php?id_lot=' . $lot->id_lot ;
if($fid) $url .= '&fid=' . $fid ;
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="fr-FR" xmlns:fb="https://www.facebook.com/2008/fbml"> 
 <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
  <meta property="fb:app_id" 	  content="<?php echo FB_ID ; ?>" />
  <meta property="og:type"   	  content="object" /> 
  <meta property="og:url"         content="<?php echo $url ; ?>"> 
  <meta property="og:locale" 	  content="<?php echo $locale ; ?>" />
  <meta property="og:locale:alternate" content="<?php echo $locale == 'fr_FR' ? 'en_US' : 'fr_FR' ; ?>" />
  <meta property="og:title"       content="<?php echo htmlentities($config->text_share_title, ENT_COMPAT, 'UTF-8') ; ?>"> 
  <meta property="og:description" content="<?php echo htmlentities($config->text_share_desc, ENT_COMPAT, 'UTF-8') ; ?>"> 
  <meta property="og:image"       content="<?php echo BASE . 'lots/' . (substr($locale, 0, 2) == 'fr' ? $lot->image_fr : $lot->image_en) ; ?>">
  <title><?php echo $config->text_lot ; ?></title>
  <script src="https://ssl.google-analytics.com/ga.js"></script>
 </head>
<body>
<?php if(GA_UID) {?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo GA_UID ; ?>']);
  _gaq.push(['_trackPageview', '/virality/partageFacebook']);
</script>
<?php } ?>
<script type="text/javascript">
if('ontouchstart' in window)
{
	window.location = 'https://apps.facebook.com/monbento-concours/<?php echo $fid ? ('?pid=' . $fid) : '' ; ?>' ;
}else{
	window.location = '<?php echo FB_URL_TAB . ($fid ? ('&app_data=pid_' . $fid) : '') ; ?>' ;
}
</script>
</body>
</html>