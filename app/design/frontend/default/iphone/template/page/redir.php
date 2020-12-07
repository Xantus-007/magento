<?php
/*
// Language redirection
if(!preg_match("#^facebook|googlebot|msnbot|yahoo|voilabot|exabot|ask jeeves|google page speed#i", $_SERVER["HTTP_USER_AGENT"])){
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) !== 'fr' && !isset($_COOKIE['lang'])){
		switch ($_SERVER['HTTP_HOST']) {
			case 'www.monbento.com':
				header('Location: http://en.monbento.com');
				break;
			case 'preprod.monbento.com':
				header('Location: http://preproden.monbento.com');
				break;
		}
		setcookie('lang', true, time()+60*60*24);
		exit;
	}
}
 */