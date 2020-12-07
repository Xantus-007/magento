<?php
session_start() ;

if(!empty($_POST) && array_key_exists('adm_login', $_POST))
{
	if($_POST['adm_login'] == 'admin' && $_POST['adm_pswd'] == ADMIN_PWD)
	{
		$_SESSION['loggued'] = 1 ;
	}
}

if($_SESSION['loggued'] == 1)
{
	
}else{
	header('Location:index.php') ;
}

?>