<?php
/**
 * The Header for our theme
 *
 * @package    WordPress
 * @since      1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<?php if ( get_theme_mod( 'penci_favicon' ) ) : ?>
		<link rel="shortcut icon" href="<?php echo get_theme_mod( 'penci_favicon' ); ?>" type="image/x-icon" />
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_theme_mod( 'penci_favicon' ); ?>">
	<?php endif; ?>
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php bloginfo( 'rss2_url' ); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo( 'name' ); ?> Atom Feed" href="<?php bloginfo( 'atom_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head();
	extract( $wp_query->query_vars );
	$classes = "";
	if(isset($maintenance)){
		$classes = $maintenance;
	}
        $gtmId = pll_current_language() === 'fr' ? 'GTM-TN8PG5X' : 'GTM-W6SKQPF';
	?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo $gtmId ?>');</script>
        <!-- End Google Tag Manager -->
</head>

<body <?php body_class($classes); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtmId ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
if( get_theme_mod( 'penci_custom_code_after_body_tag' ) ):
	echo do_shortcode( get_theme_mod( 'penci_custom_code_after_body_tag' ) );
endif;
?>
<?php
$penci_hide_header = '';
if ( is_page() ) {
	$penci_hide_header = get_post_meta( get_the_ID(), 'penci_page_hide_header', true );
}
$logo_url = esc_url( home_url('/') );
$logo_url_nav = esc_url( home_url('/') );
if( get_theme_mod('penci_custom_url_logo') ) {
	$logo_url = get_theme_mod('penci_custom_url_logo');
}
if( get_theme_mod('penci_custom_url_logo_vertical') ) {
	$logo_url_nav = get_theme_mod('penci_custom_url_logo_vertical');
}
$logo_src = get_template_directory_uri() . '/images/logo.png';
if( get_theme_mod( 'penci_logo' ) ) {
	$logo_src = get_theme_mod( 'penci_logo' );
}
/**
 * Get header layout in your customizer to change header layout
 *
 * @author PenciDesign
 */
$header_layout = get_theme_mod( 'penci_header_layout' );
$menu_style = get_theme_mod( 'penci_header_menu_style' ) ? get_theme_mod( 'penci_header_menu_style' ) : 'menu-style-1';
if ( ! isset( $header_layout ) || empty( $header_layout ) ) {
	$header_layout = 'header-1';
}
$header_class = $header_layout;
if( $header_layout == 'header-9' ) {
	$header_class = 'header-6 header-9';
}
?>
<?php
if ( get_theme_mod( 'penci_vertical_nav_show' ) ) {
	get_template_part( 'template-parts/menu-hamburger' );
}
?>
<?php if ( ! get_theme_mod( 'penci_vertical_nav_show' ) ) { ?>
<a id="close-sidebar-nav" class="<?php echo esc_attr( $header_layout ); ?>"><i class="fa fa-close"></i></a>
<nav id="sidebar-nav" class="<?php echo esc_attr( $header_layout ); ?>" role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">

	<?php if ( ! get_theme_mod( 'penci_header_logo_vertical' ) ) : ?>
		<div id="sidebar-nav-logo">
			<?php if ( get_theme_mod( 'penci_mobile_nav_logo' ) ) { ?>
				<a href="<?php echo $logo_url_nav; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo esc_url( get_theme_mod( 'penci_mobile_nav_logo' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
			<?php } elseif( get_theme_mod( 'penci_logo' ) ) { ?>
				<a href="<?php echo $logo_url_nav; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo esc_url( get_theme_mod( 'penci_logo' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
			<?php } else { ?>
				<a href="<?php echo $logo_url_nav; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo get_template_directory_uri(); ?>/images/mobile-logo.png" alt="<?php bloginfo( 'name' ); ?>" /></a>
			<?php } ?>
		</div>
	<?php endif; ?>

	<?php if ( ! get_theme_mod( 'penci_header_social_vertical' ) ) : ?>
		<div class="header-social sidebar-nav-social<?php if( get_theme_mod('penci_header_social_vertical_brand') ): echo ' penci-social-textcolored'; endif; ?>">
			<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
		</div>
	<?php endif; ?>

	<?php
	/**
	 * Display main navigation
	 */
	wp_nav_menu( array(
		'container'      => false,
		'theme_location' => 'main-menu',
		'menu_class'     => 'menu',
		'fallback_cb'    => 'penci_menu_fallback',
		'walker'         => new penci_menu_walker_nav_menu()
	) );
	?>
</nav>
<?php } ?>

<!-- .wrapper-boxed -->
<div class="wrapper-boxed header-style-<?php echo esc_attr( $header_layout ); ?><?php if ( get_theme_mod( 'penci_body_boxed_layout' ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) : echo ' enable-boxed'; endif;?><?php if ( get_theme_mod( 'penci_enable_dark_layout' ) ) : echo ' dark-layout-enabled'; endif;?><?php if( $penci_hide_header ): echo ' penci-page-hide-header'; endif; ?><?php if( get_theme_mod( 'penci_header_logo_mobile_center' ) ): echo ' penci-hlogo-center'; endif; ?>">

<?php if( ! $penci_hide_header ): ?>
	<!-- Top Instagram -->
	<?php if ( is_active_sidebar( 'top-instagram' ) ): ?>
		<div class="footer-instagram penci-top-instagram<?php if( get_theme_mod('penci_top_insta_overlay_image') ): echo ' penci-insta-title-overlay'; endif; ?>">
			<?php dynamic_sidebar( 'top-instagram' ); ?>
		</div>
	<?php endif; ?>

	<!-- Top Bar -->
	<?php if( get_theme_mod( 'penci_top_bar_show' ) ): ?>
		<?php get_template_part( 'inc/modules/topbar' ); ?>
	<?php endif; ?>

	<?php if ( in_array( $header_layout, array( 'header-1', 'header-4', 'header-7' ) ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) : ?>
	<!-- Navigation -->
	<nav id="navigation" class="header-layout-top <?php echo esc_attr( $menu_style . ' ' . $header_class ); ?><?php if( get_theme_mod( 'penci_header_enable_padding' ) ): echo ' menu-item-padding'; endif; ?><?php if( get_theme_mod( 'penci_disable_sticky_header' ) ): echo ' penci-disable-sticky-nav'; endif; /* Check for disable sticky header */ ?>" role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
		<div class="container">
			<div class="button-menu-mobile <?php echo esc_attr( $header_layout ); ?>"><i class="fa fa-bars"></i></div>
			<?php if( get_theme_mod( 'penci_header_logo_mobile' ) ): ?>
			<div class="penci-mobile-hlogo">
				<a href="<?php echo esc_url( $logo_url ); ?>"><img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
			</div>
			<?php endif; ?>
			<?php
			/**
			 * Display main navigation
			 */
			wp_nav_menu( array(
				'container'      => false,
				'theme_location' => 'main-menu',
				'menu_class'     => 'menu',
				'fallback_cb'    => 'penci_menu_fallback',
				'walker'         => new penci_menu_walker_nav_menu()
			) );
			?>

			<?php if ( get_theme_mod( 'penci_header_social_nav' ) && ( ( get_theme_mod( 'penci_header_layout' ) == 'header-4' ) || ( get_theme_mod( 'penci_header_layout' ) == 'header-5' ) ) ) : ?>
				<div class="main-nav-social<?php if( get_theme_mod('penci_header_social_brand') ): echo ' penci-social-textcolored'; endif; ?>">
					<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( class_exists( 'WooCommerce' ) && ! get_theme_mod( 'penci_woo_shop_hide_cart_icon' ) && ( ( get_theme_mod( 'penci_header_layout' ) == 'header-4' ) || ( get_theme_mod( 'penci_header_layout' ) == 'header-5' ) ) ): ?>
				<div id="top-search" class="shoping-cart-icon<?php if( get_theme_mod( 'penci_topbar_search_check' ) ): echo ' clear-right'; endif; ?>"><a class="cart-contents" href="<?php $cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url(); echo $cart_link; ?>" title="<?php _e( 'View your shopping cart' ); ?>"><i class="fa fa-shopping-cart"></i><span><?php echo sprintf (_n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span></a></div>
			<?php endif; ?>
			<?php
			if( get_theme_mod( 'penci_header_layout' ) == 'header-4' ){
				get_template_part( 'template-parts/menu-hamburger-icon' );
			}
			?>
			<?php if ( ! get_theme_mod( 'penci_topbar_search_check' ) ) : ?>
				<div id="top-search" class="dfdf">
					<a class="search-click"><i class="fa fa-search"></i></a>
					<div class="show-search">
						<?php get_search_form(); ?>
						<a class="search-click close-search"><i class="fa fa-close"></i></a>
					</div>
				</div>
			<?php endif; ?>
			<?php
			if( get_theme_mod( 'penci_header_layout' ) != 'header-4' ){
				get_template_part( 'template-parts/menu-hamburger-icon' );
			}
			?>
			<?php if ( class_exists( 'WooCommerce' ) && ! get_theme_mod( 'penci_woo_shop_hide_cart_icon' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-4' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-5' ) ): ?>
				<div id="top-search" class="shoping-cart-icon<?php if( get_theme_mod( 'penci_topbar_search_check' ) ): echo ' clear-right'; endif; ?>"><a class="cart-contents" href="<?php $cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url(); echo $cart_link; ?>" title="<?php _e( 'View your shopping cart' ); ?>"><i class="fa fa-shopping-cart"></i><span><?php echo sprintf (_n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span></a></div>
			<?php endif; ?>
			<div class="main-nav-social <?php if( get_theme_mod('penci_header_social_brand') ): echo ' penci-social-textcolored'; endif; ?>">
				<ul class="pll_lang" >
					<?php pll_the_languages( array( 'show_flags' => 1,'show_names' => 0 ) ); ?>
				</ul>
			<?php if ( get_theme_mod( 'penci_header_social_nav' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-4' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-5' ) ) : ?>
					<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
				</div>
			<?php endif; ?>

		</div>
	</nav><!-- End Navigation -->
	<?php endif; /* End check if header layout is 1, 4, 7 */?>

	<header id="header" class="header-<?php echo esc_attr( $header_layout ); ?><?php if( ( ( ! is_home() || ! is_front_page() ) && ! get_theme_mod( 'penci_featured_slider_all_page' ) ) || ( ( is_home() || is_front_page() ) && ! get_theme_mod( 'penci_featured_slider' ) ) ): ?> has-bottom-line<?php endif;?><?php if( get_theme_mod( 'penci_vertical_nav_remove_header' ) && get_theme_mod( 'penci_vertical_nav_show' ) ): echo ' penci-vernav-hide-innerhead'; endif; ?>" itemscope="itemscope" itemtype="https://schema.org/WPHeader"><!-- #header -->
		<?php if ( $header_layout != 'header-6' && $header_layout != 'header-9' && ! get_theme_mod( 'penci_vertical_nav_remove_header' ) ): ?>
		<div class="inner-header">
			<div class="container<?php if( $header_layout == 'header-3' ): echo ' align-left-logo'; if( get_theme_mod( 'penci_header_3_banner' ) || get_theme_mod( 'penci_header_3_adsense' ) ): echo ' has-banner'; endif; endif;?>">

				<div id="logo">
					<a href="<?php echo $logo_url; ?>"><img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
					<?php // if( ( is_home() || is_front_page() ) && get_theme_mod('penci_home_h1content') ): echo '<h1 class="penci-hide-tagupdated">'. get_theme_mod('penci_home_h1content') .'</h1>'; endif; ?>
				</div>

				<?php if( ( get_theme_mod( 'penci_header_3_adsense' ) || get_theme_mod( 'penci_header_3_banner' ) ) && $header_layout == 'header-3' ): ?>
					<?php
					$banner_img = get_theme_mod( 'penci_header_3_banner' ) ? get_theme_mod( 'penci_header_3_banner' ) : get_stylesheet_directory_uri() . '/images/banner-770x90.jpg';
					$open_banner_url = '';
					$close_banner_url = '';
					if( get_theme_mod( 'penci_header_3_banner_url' ) ):
						$banner_url = get_theme_mod( 'penci_header_3_banner_url' );
						$open_banner_url = '<a href="'. esc_url( $banner_url ) .'" target="_blank">';
						$close_banner_url = '</a>';
					endif;
					?>
					<div class="header-banner header-style-3">
						<?php if( get_theme_mod( 'penci_header_3_adsense' ) ):  echo do_shortcode( get_theme_mod( 'penci_header_3_adsense' ) ); endif; ?>
						<?php if( get_theme_mod( 'penci_header_3_banner' ) && ! get_theme_mod( 'penci_header_3_adsense' ) ): ?>
							<?php echo wp_kses( $open_banner_url, penci_allow_html() ); ?><img src="<?php echo esc_url( $banner_img ); ?>" alt="Banner" /><?php echo wp_kses( $close_banner_url, penci_allow_html() ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( penci_get_setting( 'penci_header_slogan_text' ) && $header_layout != 'header-3' ) : ?>
					<div class="header-slogan">
					<?php if( ( is_home() || is_front_page() )): ?>
						<?php if (get_locale() == 'fr_FR') { ?>
							<h1 class="header-slogan-text"><?php echo penci_get_setting( 'penci_header_slogan_text' ); ?></h1>
						<?php }else{ ?>
							<h1 class="header-slogan-text"><?php echo get_theme_mod('penci_header_slogan_text-en'); ?></h1>
						<?php } ?>
					<?php else: ?>
						<?php if (get_locale() == 'fr_FR') { ?>
							<span class="header-slogan-text"><?php echo penci_get_setting( 'penci_header_slogan_text' ); ?></span>
						<?php }else{ ?>
							<span class="header-slogan-text"><?php echo get_theme_mod('penci_header_slogan_text-en'); ?></span>
						<?php } ?>

					<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! get_theme_mod( 'penci_header_social_check' ) && $header_layout != 'header-3' ) : ?>
					<div class="header-social<?php if( get_theme_mod('penci_header_social_brand') ): echo ' penci-social-textcolored'; endif; ?>">
						<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; /* End check if header is layout 6, 9 */ ?>

		<?php if ( in_array( $header_layout, array( 'header-2', 'header-3', 'header-5', 'header-6', 'header-8', 'header-9' ) ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) : ?>
			<!-- Navigation -->
			<nav id="navigation" class="header-layout-bottom <?php echo esc_attr( $menu_style . ' ' . $header_class ); ?><?php if( get_theme_mod( 'penci_header_enable_padding' ) ): echo ' menu-item-padding'; endif; ?><?php if( get_theme_mod( 'penci_disable_sticky_header' ) ): echo ' penci-disable-sticky-nav'; endif; /* Check for disable sticky header */ ?>" role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
				<div class="container">
					<div class="button-menu-mobile <?php echo esc_attr( $header_layout ); ?>"><i class="fa fa-bars"></i></div>
					<?php if( in_array( $header_layout, array( 'header-2', 'header-3', 'header-5', 'header-8' ) ) && get_theme_mod( 'penci_header_logo_mobile' ) ): ?>
					<div class="penci-mobile-hlogo">
						<a href="<?php echo esc_url( $logo_url ); ?>"><img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
					</div>
					<?php endif; ?>
					<?php if ( $header_layout == 'header-6' || $header_layout == 'header-9' ): ?>
						<div id="logo">
							<a href="<?php echo esc_url( $logo_url ); ?>"><img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
							<?php // if( ( is_home() || is_front_page() ) && get_theme_mod('penci_home_h1content') ): echo '<h1 class="penci-hide-tagupdated">'. get_theme_mod('penci_home_h1content') .'</h1>'; endif; ?>
						</div>
					<?php endif; ?>
					<?php
					/**
					 * Display main navigation
					 */
					wp_nav_menu( array(
						'container'      => false,
						'theme_location' => 'main-menu',
						'menu_class'     => 'menu',
						'fallback_cb'    => 'penci_menu_fallback',
						'walker'         => new penci_menu_walker_nav_menu()
					) );
					?>

					<?php if ( get_theme_mod( 'penci_header_social_nav' ) && ( ( get_theme_mod( 'penci_header_layout' ) == 'header-4' ) || ( get_theme_mod( 'penci_header_layout' ) == 'header-5' ) ) ) : ?>
						<div class="main-nav-social<?php if( get_theme_mod('penci_header_social_brand') ): echo ' penci-social-textcolored'; endif; ?>">
							<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( class_exists( 'WooCommerce' ) && ! get_theme_mod( 'penci_woo_shop_hide_cart_icon' ) && ( ( get_theme_mod( 'penci_header_layout' ) == 'header-4' ) || ( get_theme_mod( 'penci_header_layout' ) == 'header-5' ) ) ): ?>
						<div id="top-search" class="shoping-cart-icon<?php if( get_theme_mod( 'penci_topbar_search_check' ) ): echo ' clear-right'; endif; ?>"><a class="cart-contents" href="<?php $cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url(); echo $cart_link; ?>" title="<?php _e( 'View your shopping cart' ); ?>"><i class="fa fa-shopping-cart"></i><span><?php echo sprintf (_n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span></a></div>
					<?php endif; ?>


					<?php if ( get_theme_mod( 'penci_header_layout' ) == 'header-5' ): ?>
						<?php get_template_part( 'template-parts/menu-hamburger-icon' ); ?>
					<?php endif; ?>

					<?php if ( ! get_theme_mod( 'penci_topbar_search_check' ) ) : ?>
						<div id="top-search">
							<a class="search-click"><i class="fa fa-search"></i></a>
							<div class="show-search">
								<?php get_search_form(); ?>
								<a class="search-click close-search"><i class="fa fa-close"></i></a>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( get_theme_mod( 'penci_header_layout' ) != 'header-5' ): ?>
						<?php get_template_part( 'template-parts/menu-hamburger-icon' ); ?>
					<?php endif; ?>
					<?php if ( class_exists( 'WooCommerce' ) && ! get_theme_mod( 'penci_woo_shop_hide_cart_icon' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-4' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-5' ) ): ?>
						<div id="top-search" class="shoping-cart-icon<?php if( get_theme_mod( 'penci_topbar_search_check' ) ): echo ' clear-right'; endif; ?>"><a class="cart-contents" href="<?php $cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url(); echo $cart_link; ?>" title="<?php _e( 'View your shopping cart' ); ?>"><i class="fa fa-shopping-cart"></i><span><?php echo sprintf (_n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span></a></div>
					<?php endif; ?>

					<?php if ( get_theme_mod( 'penci_header_social_nav' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-4' ) && ( get_theme_mod( 'penci_header_layout' ) != 'header-5' ) ) : ?>
						<div class="main-nav-social<?php if( get_theme_mod('penci_header_social_brand') ): echo ' penci-social-textcolored'; endif; ?>">
							<?php include( trailingslashit( get_template_directory() ). 'inc/modules/socials.php' ); ?>
						</div>
					<?php endif; ?>

				</div>
			</nav><!-- End Navigation -->
		<?php endif; /* End check if header layout is layout 2, 3, 5, 6, 8, 9 */?>
	</header>
	<!-- end #header -->
	<?php
	/**
		* Display sign-up mailchimp form below the header
		* Check if 'header-signup-form' has widget, if true display it
		*
		* @since 2.0
		*/
		if( ( ( is_home() || is_front_page() ) && get_theme_mod( 'penci_signup_display_homepage' ) ) || ! get_theme_mod( 'penci_signup_display_homepage' ) ):
			if ( is_active_sidebar( 'header-signup-form' ) && ! get_theme_mod( 'penci_move_signup_below' ) ): ?>
			<div class="penci-header-signup-form">
				<div class="container">
					<?php dynamic_sidebar( 'header-signup-form' ); ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	/**
	 * Get feature slider
	 */
	if( is_home() || get_theme_mod( 'penci_featured_slider_all_page' ) ) {
		if( get_theme_mod( 'penci_enable_featured_video_bg' ) && get_theme_mod( 'penci_featured_video_url' ) ) {
			get_template_part( 'inc/featured_slider/featured_video' );
		} else {
			if ( get_theme_mod( 'penci_featured_slider' ) == true ) :
				$slider_style = get_theme_mod( 'penci_featured_slider_style' ) ? get_theme_mod( 'penci_featured_slider_style' ) : 'style-1';

				if( ( $slider_style == 'style-33' || $slider_style == 'style-34' ) && get_theme_mod( 'penci_feature_rev_sc' ) ) {
					$rev_shortcode = get_theme_mod( 'penci_feature_rev_sc' );
					echo '<div class="featured-area featured-' . $slider_style . '">';
					if( $slider_style == 'style-34' ): echo '<div class="container">'; endif;
					echo do_shortcode( $rev_shortcode );
					if( $slider_style == 'style-34' ): echo '</div>'; endif;
					echo '</div>';
				} else {
					if ( get_theme_mod( 'penci_body_boxed_layout' ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) {
						if( $slider_style == 'style-3' ) {
							$slider_style == 'style-1';
						} elseif( $slider_style == 'style-5' ) {
							$slider_style == 'style-4';
						} elseif( $slider_style == 'style-7' ) {
							$slider_style == 'style-8';
						} elseif( $slider_style == 'style-9' ) {
							$slider_style == 'style-10';
						} elseif( $slider_style == 'style-11' ) {
							$slider_style == 'style-12';
						} elseif( $slider_style == 'style-13' ) {
							$slider_style == 'style-14';
						} elseif( $slider_style == 'style-15' ) {
							$slider_style == 'style-16';
						} elseif( $slider_style == 'style-17' ) {
							$slider_style == 'style-18';
						} elseif( $slider_style == 'style-29' ) {
							$slider_style == 'style-30';
						} elseif( $slider_style == 'style-35' ) {
							$slider_style == 'style-36';
						}
					}
					$slider_class = $slider_style;
					if( $slider_style == 'style-5' ) {
						$slider_class = 'style-4 style-5';
					} elseif ( $slider_style == 'style-30' ) {
						$slider_class = 'style-29 style-30';
					} elseif ( $slider_style == 'style-36' ) {
						$slider_class = 'style-35 style-36';
					}
					$data_auto = 'false';
					$data_loop = 'true';
					$data_res = '';

					if( $slider_style == 'style-7' || $slider_style == 'style-8' ){
						$data_res = ' data-item="4" data-desktop="4" data-tablet="2" data-tabsmall="1"';
					} elseif( $slider_style == 'style-9' || $slider_style == 'style-10' ){
						$data_res = ' data-item="3" data-desktop="3" data-tablet="2" data-tabsmall="1"';
					} elseif( $slider_style == 'style-11' || $slider_style == 'style-12' ){
						$data_res = ' data-item="2" data-desktop="2" data-tablet="2" data-tabsmall="1"';
					} elseif( in_array( $slider_style, array( 'style-31', 'style-32', 'style-35', 'style-36', 'style-37' ) ) ) {
						$data_next_prev = get_theme_mod( 'penci_enable_next_prev_penci_slider' ) ? 'true' : 'false';
						$data_dots = get_theme_mod( 'penci_disable_dots_penci_slider' ) ? 'false' : 'true';
						$data_res = ' data-dots="'. $data_dots .'" data-nav="'. $data_next_prev .'"';
					}

					if( get_theme_mod( 'penci_featured_autoplay' ) ): $data_auto = 'true'; endif;
					if( get_theme_mod( 'penci_featured_loop' ) ): $data_loop = 'false'; endif;
					$auto_time = get_theme_mod( 'penci_featured_slider_auto_time' );
					if( !is_numeric( $auto_time ) ): $auto_time = '4000'; endif;
					$auto_speed = get_theme_mod( 'penci_featured_slider_auto_speed' );
					if( !is_numeric( $auto_speed ) ): $auto_speed = '600'; endif;
					$open_container = '';
					$close_container = '';
					if( in_array( $slider_style, array( 'style-1', 'style-4', 'style-6', 'style-8', 'style-10', 'style-12', 'style-14', 'style-16', 'style-18', 'style-19', 'style-20', 'style-21', 'style-22', 'style-23', 'style-24', 'style-25', 'style-26', 'style-27', 'style-30', 'style-32', 'style-36', 'style-37' ) ) ):
						$open_container = '<div class="container">';
						$close_container = '</div>';
					endif;

					if( get_theme_mod( 'penci_enable_flat_overlay' ) && in_array( $slider_style, array( 'style-6', 'style-7', 'style-8', 'style-9', 'style-10', 'style-11', 'style-12', 'style-13', 'style-14', 'style-15', 'style-16', 'style-17', 'style-18', 'style-19', 'style-20', 'style-21', 'style-22', 'style-23', 'style-24', 'style-25', 'style-26', 'style-27', 'style-28' ) ) ): $slider_class .= ' penci-flat-overlay'; endif;

					echo '<div class="featured-area featured-' . $slider_class . '">' . $open_container;
					if( $slider_style == 'style-37' ):
					echo '<div class="penci-featured-items-left">';
					endif;
					echo '<div class="penci-owl-carousel penci-owl-featured-area"'. $data_res .'data-style="'. $slider_style .'" data-auto="'. $data_auto .'" data-autotime="'. $auto_time .'" data-speed="'. $auto_speed .'" data-loop="'. $data_loop .'">';
					get_template_part( 'inc/featured_slider/' . $slider_style );
					echo '</div>';
					echo $close_container. '</div>';
				}
			endif;
		}
	}
	?>
	<?php
	/**
	 * Display sign-up mailchimp form below the header
	 * Check if 'header-signup-form' has widget, if true display it
	 *
	 * @since 2.0
	 */
	if( ( ( is_home() || is_front_page() ) && get_theme_mod( 'penci_signup_display_homepage' ) ) || ! get_theme_mod( 'penci_signup_display_homepage' ) ):
		if ( is_active_sidebar( 'header-signup-form' ) && get_theme_mod( 'penci_move_signup_below' ) ):
			if( ! get_theme_mod( 'penci_move_signup_full_width' ) ){
		?>
			<div class="container penci-header-signup-form penci-header-signup-form-below">
				<?php dynamic_sidebar( 'header-signup-form' ); ?>
			</div>
		<?php } else { ?>
			<div class="penci-header-signup-form penci-header-signup-form-below">
				<div class="container">
					<?php dynamic_sidebar( 'header-signup-form' ); ?>
				</div>
			</div>
		<?php } ?>
		<?php endif; ?>
	<?php endif; /* display header signup form only on homepage */ ?>
<?php endif; // Hide header ?>
