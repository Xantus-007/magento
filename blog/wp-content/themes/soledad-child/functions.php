<?php

// Adding post type for youtube videos


/*
* Creating a function to create our CPT ( Youtube videos )
*/
 
function custom_post_type() {
 
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Youtube videos', 'Post Type General Name', 'soledad' ),
        'singular_name'       => _x( 'Youtube video', 'Post Type Singular Name', 'soledad' ),
        'menu_name'           => __( 'Youtube videos', 'soledad' ),
        'parent_item_colon'   => __( 'Parent Youtube video', 'soledad' ),
        'all_items'           => __( 'All Youtube videos', 'soledad' ),
        'view_item'           => __( 'View Youtube video', 'soledad' ),
        'add_new_item'        => __( 'Add New Youtube video', 'soledad' ),
        'add_new'             => __( 'Add New', 'soledad' ),
        'edit_item'           => __( 'Edit Youtube video', 'soledad' ),
        'update_item'         => __( 'Update Youtube video', 'soledad' ),
        'search_items'        => __( 'Search Youtube video', 'soledad' ),
        'not_found'           => __( 'Not Found', 'soledad' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'soledad' ),
    );
     
// Set other options for Custom Post Type
     
    $args = array(
        'label'               => __( 'Youtube videos', 'monebnto' ),
        'description'         => __( 'Youtube videos for recipes will be shown in the home page', 'monebnto' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions' ),
		'menu_icon'      => 'dashicons-video-alt2',
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
     
    // Registering your Custom Post Type
    register_post_type( 'VideosYoutube', $args );
 
}
 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'custom_post_type', 0 );

 
define('DEVELOPER_KEY', get_theme_mod('youtubeConsolekey'));

function getVideoInformation( $videoUrl = null , $url = false) {
	$YID = $videoUrl;
	if($url){
		$my_array_of_vars = array();
		//  get youtube ID from an URL
		parse_str( parse_url( $videoUrl, PHP_URL_QUERY ), $my_array_of_vars);
		$YID = $my_array_of_vars['v'];

	}
	$url = "https://www.googleapis.com/youtube/v3/videos?id=$YID&part=id%2C+snippet%2C+statistics&key=".DEVELOPER_KEY;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$arr_result = json_decode($response); 
	return $arr_result;
}

// Youtube Options in customizer
function soledad_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'youtubeConsolekey_section' , array(
	'title'      => __( 'Youtube API Options', 'soledad' ),
	'priority'   => 30,
	) );
	$wp_customize->add_setting( 'youtubeConsolekey' , array(
		'default'        => 'AIzaSyCUWOLa0gJFEzHJ0EnIePfXdqJHnBRWPzc',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'KeyIDYoutube', array(
		'label'      => __( 'API Key youtube', 'soledad' ),
		'section'    => 'youtubeConsolekey_section',
		'settings'   => 'youtubeConsolekey',
		'type'	     => 'text',
	) ) );
	$wp_customize->add_setting( 'youtubeImageSize' , array(
		'default' => 'standard',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
   	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'YoutubeImageSize', array(
		'label'      => __( 'Image resolution', 'soledad' ),
		'section'    => 'youtubeConsolekey_section',
		'settings'   => 'youtubeImageSize',
		'type'	     => 'select',
		'choices' => array( // Optional.
			'default' => __( 'Default' ),
			'medium' => __( 'Medium' ),
			'high' => __( 'High' ),
			'standard' => __( 'Standard' ),
			'maxres' => __( 'Resolution maximal' ),
      	)
	) ) );
}
add_action( 'customize_register', 'soledad_customize_register' );

// Mailjet Options in customizer
function mailjet_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'mailjet_section' , array(
	'title'      => __( 'Mailjet API Options', 'soledad' ),
	'priority'   => 30,
	) );
	$wp_customize->add_setting( 'mailjetApiKey' , array(
		'default'        => '',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'KeyMailjet', array(
		'label'      => __( 'API Key mailjet', 'soledad' ),
		'section'    => 'mailjet_section',
		'settings'   => 'mailjetApiKey',
		'type'	     => 'text',
	) ) );
	$wp_customize->add_setting( 'mailjetApiKeySecrect' , array(
		'default'        => '',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'SecretMailjet', array(
		'label'      => __( 'API Secret Key mailjet', 'soledad' ),
		'section'    => 'mailjet_section',
		'settings'   => 'mailjetApiKeySecrect',
		'type'	     => 'password',
	) ) );
	$wp_customize->add_setting( 'mailjetListID' , array(
		'default'        => '',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'IDMailjet', array(
		'label'      => __( 'List ID mailjet', 'soledad' ),
		'section'    => 'mailjet_section',
		'settings'   => 'mailjetListID',
		'type'	     => 'text',
	) ) );
	$wp_customize->add_setting( 'mailjetListID-en' , array(
		'default'        => '',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'IDMailjet-en', array(
		'label'      => __( 'List ID mailjet EN', 'soledad' ),
		'section'    => 'mailjet_section',
		'settings'   => 'mailjetListID-en',
		'type'	     => 'text',
	) ) );
	
}
add_action( 'customize_register', 'mailjet_customize_register' );

/**
 * Include default fonts support by browser
 *
 * @since 2.0
 * @return array list $penci_font_browser_arr
 */
if ( ! function_exists( 'penci_font_browser' ) ) {
	function penci_font_browser() {
		$penci_font_browser_arr = array();
		$penci_font_browser     = array(
			'Playfair Display',
			'Sofia Pro Bold Condensed',
			'Sofia Pro',
			'Arial, Helvetica, sans-serif',
			'"Comic Sans MS", cursive, sans-serif',
			'Impact, Charcoal, sans-serif',
			'"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Tahoma, Geneva, sans-serif',
			'"Trebuchet MS", Helvetica, sans-serif',
			'Verdana, Geneva, sans-serif',
			'Georgia, serif',
			'"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'"Times New Roman", Times, serif',
			'"Courier New", Courier, monospace',
			'"Lucida Console", Monaco, monospace',
		);
		foreach ( $penci_font_browser as $font ) {
			$penci_font_browser_arr[$font] = $font;
		}

		return $penci_font_browser_arr;
	}
}
add_action( 'wp_enqueue_scripts', 'Egio_load_scripts' );

function Egio_load_scripts() {
	wp_enqueue_script( 'mobento_validation', get_stylesheet_directory_uri() . '/js/jquery.validate.min.js' , array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'mobento_underscore', get_stylesheet_directory_uri() . '/js/underscore.js' , array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'main-scripts', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery' ), '6.3.2', true );
	wp_enqueue_script( 'mobento_scripts', get_stylesheet_directory_uri() . '/js/scripts.js' , array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'mobento_ytb', get_stylesheet_directory_uri() . '/js/iframe_api.js' , array( 'jquery' ), '1.0', true );
	//wp_enqueue_script( 'mobento_recaptcha', 'https://google.com/recaptcha/api.js?onload=initRecaptcha&render=explicit' , array( 'jquery' ), '1.0', true );
}
// Subscription in newsletter Mailjet
//require __DIR__.'/vendor/autoload.php';
use Mailjet\Client;
use Mailjet\Resources;

// creating widget for the subscription
include(  __DIR__. '/inc/widgets/newsletter_widget.php' );

add_action('wp_ajax_nopriv_addSubscriber', 'addSubscriber');
add_action( 'wp_ajax_addSubscriber','addSubscriber');
add_action('wp_ajax_nopriv_SendConfirmationMail', 'SendConfirmationMail');
add_action( 'wp_ajax_SendConfirmationMail','SendConfirmationMail');

function SendConfirmationMail() {
	$email = $_POST['email'];
	$offres_promo = $_POST['offres_promo'];
	$infos_et_news = $_POST['infos_et_news'];
	$recettes = $_POST['recettes'];
	$to = $email;
	$subject = __('Confirm Your Subscription Monbento','soledad');
	$body = '<div style="background:#f4f4f4"><div class="m_-147667512475593040mj-container" style="background-color:#f4f4f4"><div style="margin:0px auto;max-width:600px"><table role="presentation" style="font-size:0px;width:100%" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-bottom:0px;padding-top:0px"><div class="m_-147667512475593040mj-column-per-100 m_-147667512475593040outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px"><div style="font-size:1px;line-height:40px;white-space:nowrap">&nbsp;</div></td></tr></tbody></table></div></td></tr></tbody></table></div><div style="margin:0px auto;max-width:600px;background:#ffffff"><table role="presentation" style="font-size:0px;width:100%;background:#ffffff" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px"><div class="m_-147667512475593040mj-column-per-100 m_-147667512475593040outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:20px" align="left"><div style="color:#5e6977;font-family:Arial,sans-serif;font-size:13px;line-height:22px;text-align:left"><h1 style="font-family:"Trebuchet MS",Helvetica,Arial,sans-serif;font-size:28px;font-weight:normal;line-height:32px"><b><span style="font-family:Arial,sans-serif">'.__("Please confirm your subscription","soledad").'</span></b></h1></div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px" align="left"><div style="color:#5e6977;font-family:Arial,sans-serif;font-size:13px;line-height:22px;text-align:left"><p style="font-size:15px;margin:10px 0">'.__("To receive newsletters from","soledad").' <a href="'.get_home_url().'" target="_blank" style="color:#243746"> Monbento </a>'.__("please confirm your subscription by clicking the following button","soledad").':</p></div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:10px 25px" align="left"><table role="presentation" style="border-collapse:separate" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td style="border:none;border-radius:3px;color:#ffffff;padding:10px 25px" valign="middle" bgcolor="#ed7389" align="center"><a  href="'.get_site_url().'/wp-admin/admin-ajax.php?action=addSubscriber&email='.$email.'&offres_promo='.$offres_promo.'&infos_et_news='.$infos_et_news.'&recettes='.$recettes.'&lang='.get_locale().'" style="text-decoration:none;background:#ed7389;color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:normal;line-height:120%;text-transform:none;margin:0px" target="_blank" ><b>'.__("Yes, subscribe me to this list","soledad").'</b></a></td></tr></tbody></table></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px" align="left"><div style="color:#5e6977;font-family:Arial,sans-serif;font-size:13px;line-height:22px;text-align:left"><p style="font-size:15px;margin:10px 0">'.__("If you received this email by mistake or don't wish to subscribe anymore, simply ignore this message","soledad").'.</p></div></td></tr></tbody></table></div></td></tr></tbody></table></div><div style="margin:0px auto;max-width:600px"><table role="presentation" style="font-size:0px;width:100%" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px 20px 0px"><div class="m_-147667512475593040mj-column-per-100 m_-147667512475593040outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px"><div style="font-size:1px;line-height:40px;white-space:nowrap">&nbsp;</div></td></tr></tbody></table></div></td></tr></tbody></table></div></div><div class="yj6qo"></div><div class="adL"></div></div>';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	
	$mailr = wp_mail( $to, $subject, $body, $headers );
	$response = array();
	$response['status']= 'done';
	$response['message']= __('A confirmation email has been sent. Please check your inbox to confirm your subscription.','soledad');

	echo json_encode($response);
	die;
}

function addSubscriber() {
	$email = $_GET['email'];
	$offres_promo = $_GET['offres_promo'];
	$infos_et_news = $_GET['infos_et_news'];
	$lang = $_GET['lang'];
	$recettes = $_GET['recettes'];
	$id = get_theme_mod('mailjetListID');
	if($lang!='fr_FR'){
		$id = get_theme_mod('mailjetListID-en');
	}
	$apikey = get_theme_mod('mailjetApiKey');
	$apisecret = get_theme_mod('mailjetApiKeySecrect');

	$mailjet = new Client($apikey, $apisecret);
	$body = [
		'Email' => $email,
		'Properties' => [
			'offres_promo' => $offres_promo,
			'infos_et_news' => $infos_et_news,
			'recettes' => $recettes,
		],
		'Action' => "addnoforce",
	];
	$result = $mailjet->post(Resources::$ContactslistManagecontact, ['id' => $id, 'body' => $body]);
	$ResultMessage= array();
	if ($result->getReasonPhrase() == "Created") {
		$ResultMessage['message'] = __('Successful registration','soledad');
		$ResultMessage['status'] = 'done';
	}else{
		$ResultMessage['message'] = __('An error occurred while subscribing to the newsletter','soledad');
		$ResultMessage['status'] = 'fail';
	}
	echo '<html><body style="margin:0"></body></html><div id="confirmation-page" style="width: 100%; background-color: white;">
    <div class="mj-confirmation-page-header mockup-content paint-area" style="background-color: #e1e1e6; text-align: center;">
        <div style="display: table; height: 90px; width: 100%;">
            <div style="display: table-cell; vertical-align: middle;">
                <div class="mj-confirmation-page-title paint-area paint-area--text" style="font-family:Ubuntu, Helvetica; display: inline-block; text-align: center; font-size: 20px; color: #333333;">
                    Newsletter Registration                </div>
            </div>
        </div>
    </div>
    <div class="mj-confirmation-page-content mockup-content paint-area" style="text-align: center;">
        <div class="mj-confirmation-page-image-place" style="padding: 50px 0;"><img src="//r.mailjet.com/w/w-confirmation-page-mail.png" alt="confirm subscription"></div>
        <div style="display: table; height: 70px; width: 100%;">
            <div style="display: table-cell; vertical-align: middle;">
                <div class="mj-confirmation-page-text paint-area paint-area--text" style="color: #aab6bd; font-family: Ubuntu, Helvetica; font-size: 22px; display: inline-block;">
                    <b class="medium-b">'.__("Congratulations, you have successfully subscribed","soledad").'!</b>
                </div>
            </div>
        </div>
    </div>
</div></body></html>';
	die();

}
function list_author_in_this_cat ($with,$args = '') {
	global $wpdb;

	$defaults = array(
		'orderby' => 'date', 'order' => 'DESC', 'number' => '',
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true,
		'includeauthorid' => false 
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );


	$query_args = wp_array_slice_assoc( $args, array( 'orderby', 'order', 'number' ) );
	$query_args['fields'] = 'ids';
	$authors = get_users( $query_args );
	$post_author = array();
	foreach ($authors as $key => $value) {
		$args = array(
			'author'        =>  $value, 
			'orderby'       =>  'post_date',
			'order'         =>  'ASC',
			'posts_per_page' => -1 // no limit
		);


		$current_user_posts = get_posts( $args );
		$total = count($current_user_posts);
		if($total>0)
			$post_author[] = $value;
	}
	return $post_author;


}

// Function to change email address
 
function wpb_sender_email( $original_email_address ) {
    return  'monbento@monbento.com';
}
 
// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return  'Monbento';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );

// Sendmail Options in customizer
function Sendmail_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'Sendmail_section' , array(
	'title'      => __( 'Send mail Options', 'soledad' ),
	'priority'   => 30,
	) );
	$wp_customize->add_setting( 'Sendmail_name' , array(
		'default'        => 'Monbento',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'Sendername', array(
		'label'      => __( 'Sender name', 'soledad' ),
		'section'    => 'Sendmail_section',
		'settings'   => 'Sendmail_name',
		'type'	     => 'text',
	) ) );
	$wp_customize->add_setting( 'Sendmail_mail' , array(
		'default'        => 'Monbento@monbento.com',
		'capability'     => 'edit_theme_options',
		'type'           => 'theme_mod',
		'transport'      => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'Sendermail', array(
		'label'      => __( 'Sender mail', 'soledad' ),
		'section'    => 'Sendmail_section',
		'settings'   => 'Sendmail_mail',
		'type'	     => 'email',
	) ) );
	
}
add_action( 'customize_register', 'Sendmail_customize_register' );
// add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
//     function onMailError( $wp_error ) {
//         echo "<pre>";
//         print_r($wp_error);
//         echo "</pre>";
// 	}
	
/**
* Maintenance mode.
*/
add_action( 'wp_loaded', 'monbento_maintenance_mode' );
function monbento_maintenance_mode() {
	global $pagenow;
	//if ( $pagenow !== 'wp-login.php' && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
	if ( $pagenow !== 'wp-login.php' && !is_admin()) {
		if (get_field('maintenance_mode', 'options')) {
			header( $_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable', true, 503 );
			header( 'Content-Type: text/html; charset=utf-8' );
			header( 'Retry-After: 600' );
			set_query_var( 'maintenance', "errormaintenance" );
			get_template_part('maintenance');
			die();
		}
	}
}

if( function_exists('acf_add_options_page') ) {

	$option_page = acf_add_options_page(array(
		'page_title' 	=> __( 'Configuration monbento', 'soledad' ),
		'menu_title' 	=> __( 'Configuration monbento', 'soledad' ),
		'menu_slug' 	=> 'theme-general-info',
		'capability' 	=> 'edit_posts',
		'redirect' 	=> false
	));


}



if ( ! function_exists( 'penci_soledad_pagination' ) ) {
	function penci_soledad_pagination() {

		if( get_theme_mod( 'penci_page_navigation_numbers' ) ) {
			echo penci_pagination_numbers();
		} else {
			global $wp_query;
			if ( $wp_query->max_num_pages > 1 ) :
				?>
				<div class="penci-pagination"> 
 
					<div class="newer"> 
						<?php if( get_next_posts_link() ) { ?> 
							<?php next_posts_link( '<span><i class="fa fa-angle-left"></i>'. penci_get_setting('penci_trans_older_posts') .' </span>' ); ?> 
						<?php } else { ?> 
							<?php echo '<div class="disable-url"><span><i class="fa fa-angle-left"></i>'. penci_get_setting('penci_trans_older_posts') .' </span></div>'; ?> 
						<?php } ?> 
					</div> 
					<div class="older"> 
						<?php if( get_previous_posts_link() ) { ?> 
							<?php previous_posts_link( '<span>'. penci_get_setting('penci_trans_newer_posts') .' <i class="fa fa-angle-right"></i> </span>' ); ?> 
						<?php } else { ?> 
							<?php echo '<div class="disable-url"><span>'. penci_get_setting('penci_trans_newer_posts') .' <i class="fa fa-angle-right"></i></span></div>'; ?> 
						<?php } ?> 
					</div> 
				</div> 
			<?php
			endif;
		}
	}
}
function admin_css() {
	$admin_handle = 'admin_css';
	$admin_stylesheet = get_stylesheet_directory_uri() . '/css/admin.css';

	wp_enqueue_style($admin_handle, $admin_stylesheet);
}
add_action('admin_print_styles', 'admin_css', 11);

add_action( 'init', 'Eg_change_post_object' );
// Change dashboard Posts to News
function Eg_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
        $labels->name = 'Recettes';
        $labels->singular_name = 'Recette';
        $labels->add_new = 'Ajouter Recette';
        $labels->add_new_item = 'Ajouter Recette';
        $labels->edit_item = 'Modifier Recette';
        $labels->new_item = 'Recette';
        $labels->view_item = 'Afficher Recette';
        $labels->search_items = 'Rechercher Recettes';
        $labels->not_found = 'Aucune Recette trouvée';
        $labels->not_found_in_trash = 'Aucune Recette trouvée dans la corbeille';
        $labels->all_items = 'Toutes les recettes';
        $labels->menu_name = 'Recettes';
        $labels->name_admin_bar = 'Recettes';
}
add_action( 'init', 'Eg_change_port_object' );
// Change dashboard Posts to News
function Eg_change_port_object() {
    $get_post_type = get_post_type_object('portfolio');
    $labels = $get_post_type->labels;
        $labels->name = 'Articles';
        $labels->singular_name = 'Article';
        $labels->add_new = 'Ajouter Article';
        $labels->add_new_item = 'Ajouter Article';
        $labels->edit_item = 'Modifier Article';
        $labels->new_item = 'Article';
        $labels->view_item = 'Afficher Article';
        $labels->search_items = 'Rechercher Articles';
        $labels->not_found = 'Aucune Article trouvée';
        $labels->not_found_in_trash = 'Aucune Article trouvée dans la corbeille';
        $labels->all_items = 'Toutes les Articles';
        $labels->menu_name = 'Articles';
        $labels->name_admin_bar = 'Articles';
}
if ( ! function_exists( 'penci_register_required_plugins' ) ) {
	function penci_register_required_plugins() {
		$link_server = 'https://s3.amazonaws.com/soledad-plugins/';

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			array(
				'name'               => 'Vafpress Post Formats UI', // The plugin name
				'slug'               => 'vafpress-post-formats-ui-develop', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'vafpress-post-formats-ui-develop.zip', // The plugin source
				'required'           => true, // If false, the plugin is only 'recommended' instead of required
				'version'            => '1.6', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Penci Shortcodes', // The plugin name
				'slug'               => 'penci-shortcodes', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'penci-shortcodes.zip', // The plugin source
				'required'           => true, // If false, the plugin is only 'recommended' instead of required
				'version'            => '2.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Penci Slider', // The plugin name
				'slug'               => 'penci-soledad-slider', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'penci-soledad-slider.zip', // The plugin source
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '1.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			// array(
			// 	'name'               => 'Penci Portfolio', // The plugin name
			// 	'slug'               => 'penci-portfolio', // The plugin slug (typically the folder name)
			// 	'source'             => $link_server . 'penci-portfolio.zip', // The plugin source
			// 	'required'           => false, // If false, the plugin is only 'recommended' instead of required
			// 	'version'            => '2.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			// 	'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			// 	'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			// 	'external_url'       => '', // If set, overrides default API URL and points to an external URL
			// ),
			// array(
			// 	'name'               => 'Penci Recipe', // The plugin name
			// 	'slug'               => 'penci-recipe', // The plugin slug (typically the folder name)
			// 	'source'             => $link_server . 'penci-recipe.zip', // The plugin source
			// 	'required'           => false, // If false, the plugin is only 'recommended' instead of required
			// 	'version'            => '2.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			// 	'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			// 	'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			// 	'external_url'       => '', // If set, overrides default API URL and points to an external URL
			// ),
			array(
				'name'               => 'Penci Review', // The plugin name
				'slug'               => 'penci-review', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'penci-review.zip', // The plugin source
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '2.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Penci Soledad Demo Importer', // The plugin name
				'slug'               => 'penci-soledad-demo-importer', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'penci-soledad-demo-importer.zip', // The plugin source
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '2.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Instagram Slider Widget', // The plugin name
				'slug'               => 'instagram-slider-widget', // The plugin slug (typically the folder name)
				'source'             => $link_server . 'instagram-slider-widget.zip', // The plugin source
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'oAuth Twitter Feed', // The plugin name
				'slug'               => 'oauth-twitter-feed-for-developers', // The plugin slug (typically the folder name)
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Contact Form 7', // The plugin name
				'slug'               => 'contact-form-7', // The plugin slug (typically the folder name)
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'MailChimp for WordPress', // The plugin name
				'slug'               => 'mailchimp-for-wp', // The plugin slug (typically the folder name)
				'required'           => false, // If false, the plugin is only 'recommended' instead of required
				'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '', // If set, overrides default API URL and points to an external URL
			)

		);

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 *
		 * Some of the strings are wrapped in a sprintf(), so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'id'           => 'tgmpa', // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '', // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php', // Parent menu slug.
			'capability'   => 'edit_theme_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true, // Show admin notices or not.
			'dismissable'  => true, // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '', // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false, // Automatically activate plugins after installation or not.
			'message'      => '', // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'soledad' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'soledad' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'soledad' ),
				// %s = plugin name.
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'soledad' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %1$s plugin.', 'Sorry, but you do not have the correct permissions to install the %1$s plugins.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_ask_to_update_maybe'      => _n_noop( 'There is an update available for: %1$s.', 'There are updates available for the following plugins: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %1$s plugin.', 'Sorry, but you do not have the correct permissions to update the %1$s plugins.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'soledad' ),
				// %1$s = plugin name(s).
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %1$s plugin.', 'Sorry, but you do not have the correct permissions to activate the %1$s plugins.', 'soledad' ),
				// %1$s = plugin name(s).
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'soledad' ),
				'update_link'                     => _n_noop( 'Begin updating plugin', 'Begin updating plugins', 'soledad' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'soledad' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'soledad' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'soledad' ),
				'activated_successfully'          => esc_html__( 'The following plugin was activated successfully:', 'soledad' ),
				'plugin_already_active'           => esc_html__( 'No action taken. Plugin %1$s was already active.', 'soledad' ),
				// %1$s = plugin name(s).
				'plugin_needs_higher_version'     => esc_html__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'soledad' ),
				// %1$s = plugin name(s).
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %1$s', 'soledad' ),
				// %s = dashboard link.
				'contact_admin'                   => esc_html__( 'Please contact the administrator of this site for help.', 'soledad' ),
				'nag_type'                        => 'updated',
				// Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);

		tgmpa( $plugins, $config );

	}
}
$roleObject = get_role( 'editor' );
if (!$roleObject->has_cap( 'edit_theme_options' ) ) {
    $roleObject->add_cap( 'edit_theme_options' );
}
 
function hide_menu() {
    // Si le role de l'utilisatieur ne lui permet pas d'ajouter des comptes (autrement dit si il n'est pas admin)
    if(!current_user_can('add_users')) {
      remove_submenu_page( 'themes.php', 'themes.php' ); // hide the theme selection submenu
      remove_submenu_page( 'themes.php', 'theme-editor.php' ); // hide the editor menu
 
      // Le code suisant c'est juste poure retirer le sous menu "Personnaliser"
      $customize_url_arr = array();
      $customize_url_arr[] = 'customize.php'; // 3.x
      $customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'customize.php' );
      $customize_url_arr[] = $customize_url; // 4.0 & 4.1
      if ( current_theme_supports( 'custom-header' ) && current_user_can( 'customize') ) {
          $customize_url_arr[] = add_query_arg( 'autofocus[control]', 'header_image', $customize_url ); // 4.1
          $customize_url_arr[] = 'custom-header'; // 4.0
      }
      if ( current_theme_supports( 'custom-background' ) && current_user_can( 'customize') ) {
          $customize_url_arr[] = add_query_arg( 'autofocus[control]', 'background_image', $customize_url ); // 4.1
          $customize_url_arr[] = 'custom-background'; // 4.0
      }
      foreach ( $customize_url_arr as $customize_url ) {
        //   remove_submenu_page( 'themes.php', $customize_url );
      }
 
    }
 
}
add_action('admin_head', 'hide_menu');

/*
 * Let Editors manage users, and run this only once.
 */
function isa_editor_manage_users() {
 
    if ( get_option( 'isa_add_cap_editor_once' ) != 'done' ) {
     
        // let editor manage users
 
        $edit_editor = get_role('editor'); // Get the user role
        $edit_editor->add_cap('edit_users');
        $edit_editor->add_cap('list_users');
        $edit_editor->add_cap('promote_users');
        $edit_editor->add_cap('create_users');
        $edit_editor->add_cap('add_users');
        $edit_editor->add_cap('delete_users');
 
        update_option( 'isa_add_cap_editor_once', 'done' );
    }
 
}
add_action( 'init', 'isa_editor_manage_users' );

//prevent editor from deleting, editing, or creating an administrator
// only needed if the editor was given right to edit users
 
class ISA_User_Caps {
 
  // Add our filters
  function __construct() {
    add_filter( 'editable_roles', array(&$this, 'editable_roles'));
    add_filter( 'map_meta_cap', array(&$this, 'map_meta_cap'),10,4);
  }
  // Remove 'Administrator' from the list of roles if the current user is not an admin
  function editable_roles( $roles ){
    if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
      unset( $roles['administrator']);
    }
    return $roles;
  }
  // If someone is trying to edit or delete an
  // admin and that user isn't an admin, don't allow it
  function map_meta_cap( $caps, $cap, $user_id, $args ){
    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }
 
}
 
$isa_user_caps = new ISA_User_Caps();

// Hide all administrators from user list.
 
add_action('pre_user_query','isa_pre_user_query');
function isa_pre_user_query($user_search) {
 
    $user = wp_get_current_user();
     
    if ( ! current_user_can( 'manage_options' ) ) {
   
        global $wpdb;
     
        $user_search->query_where = 
            str_replace('WHERE 1=1', 
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')", 
            $user_search->query_where
        );
 
    }
}
function custom_post_type2() {
 
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'produits associés', 'Post Type General Name', 'soledad' ),
        'singular_name'       => _x( 'produit associé', 'Post Type Singular Name', 'soledad' ),
        'menu_name'           => __( 'produits associés', 'soledad' ),
        'parent_item_colon'   => __( 'Parent produit associé', 'soledad' ),
        'all_items'           => __( 'Tous les produits associés', 'soledad' ),
        'view_item'           => __( 'Voir les produit associé', 'soledad' ),
        'add_new_item'        => __( 'Ajouter un nouveau produit associé', 'soledad' ),
        'add_new'             => __( 'Ajouter un nouveau', 'soledad' ),
        'edit_item'           => __( 'Editer le produit associé', 'soledad' ),
        'update_item'         => __( 'Mettre à jour le produit associé', 'soledad' ),
        'search_items'        => __( 'Rechercher un produit associé', 'soledad' ),
        'not_found'           => __( 'Pas trouvé', 'soledad' ),
        'not_found_in_trash'  => __( 'Pas trouvé dans la corbeille', 'soledad' ),
    );
     
// Set other options for Custom Post Type
     
    $args = array(
        'label'               => __( 'produits associés', 'monebnto' ),
        'description'         => __( '', 'monebnto' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions' ),
		'menu_icon'      => 'dashicons-products',
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
     
    // Registering your Custom Post Type
    register_post_type( 'productss', $args );
 
}
add_action( 'init', 'custom_post_type2', 0 );
 