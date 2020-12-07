<?php
$classes = 'penci-vernav-hide';
$pos = get_theme_mod( 'penci_menu_hbg_pos' );
$pos = $pos ? $pos : 'left';

$hide_logo   = get_theme_mod( 'penci_menu_hbg_hide_logo' );
$hide_social = get_theme_mod( 'penci_menu_hbg_hidesocial' );

$footer_text = get_theme_mod( 'penci_menu_hbg_footer_text' );
$social_style = get_theme_mod( 'penci_menuhbg_social_style' ) ? get_theme_mod( 'penci_menuhbg_social_style' ) : 'style-1';

$heading_title = 'style-1';
if( get_theme_mod( 'penci_mhbgwidget_heading_style' ) ) {
	$heading_title = get_theme_mod( 'penci_mhbgwidget_heading_style' );
}elseif(  get_theme_mod( 'penci_sidebar_heading_style' ) ){
	$heading_title = get_theme_mod( 'penci_sidebar_heading_style' );
}

$heading_align = 'pcalign-center';
if( get_theme_mod( 'penci_mhbgwidget_heading_align' ) ) {
	$heading_align = get_theme_mod( 'penci_mhbgwidget_heading_align' );
}elseif(  get_theme_mod( 'penci_sidebar_heading_align' ) ){
	$heading_align = get_theme_mod( 'penci_sidebar_heading_align' );
}
$logo_url_hamburger = esc_url( home_url('/') );
if( get_theme_mod('penci_custom_logo_hamburger') ) {
	$logo_url_hamburger = get_theme_mod('penci_custom_logo_hamburger');
}
if( get_theme_mod('penci_vertical_nav_show') ) {
	$classes = 'penci-vernav-show';
}
?>
<?php if( $classes == 'penci-vernav-hide' ): ?>
<div class="penci-menu-hbg-overlay"></div>
<?php endif; ?>
<?php if( $classes == 'penci-vernav-show' ): ?>
	<a class="penci-vernav-toggle" href="#"><i class="fa fa-bars"></i></a>
<?php endif; ?>
<div class="penci-menu-hbg <?php echo $classes; ?> penci-menu-hbg-<?php echo esc_attr( $pos ); ?>">
	<div class="penci-menu-hbg-inner">
		<?php if( $classes == 'penci-vernav-hide' ): ?>
		<a id="penci-close-hbg"><i class="fa fa-close"></i></a>
		<?php endif; ?>
		<?php if( ! get_theme_mod( 'penci_menu_hbg_hide_logo' ) || get_theme_mod( 'penci_menu_hbg_sitetitle' ) || get_theme_mod( 'penci_menu_hbg_desc' ) ): ?>
		<div class="penci-hbg-header">
			<?php
			$hbg_sitetitle = get_theme_mod( 'penci_menu_hbg_sitetitle' );
			$hbg_desc      = get_theme_mod( 'penci_menu_hbg_desc' );
			if ( ! $hide_logo ) {
				$logo_img      = get_theme_mod( 'penci_menu_hbg_logo' );
				?>
				<div class="penci-hbg-logo site-branding">
					<?php if ( $logo_img ) { ?>
						<a href="<?php echo $logo_url_hamburger; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo esc_url( $logo_img ); ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
					<?php } elseif ( get_theme_mod( 'penci_logo' ) ) { ?>
						<a href="<?php echo $logo_url_hamburger; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo esc_url( get_theme_mod( 'penci_logo' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
					<?php } else { ?>
						<a href="<?php echo $logo_url_hamburger; ?>"><img class="penci-lazy" src="<?php echo get_template_directory_uri() . '/images/penci-holder.png'; ?>" data-src="<?php echo get_template_directory_uri(); ?>/images/mobile-logo.png" alt="<?php bloginfo( 'name' ); ?>"/></a>
					<?php } ?>
				</div>
				<?php
			}
			
			if ( $hbg_sitetitle ) {
				echo '<div class="penci-hbg_sitetitle">' . do_shortcode( $hbg_sitetitle ) . '</div>';
			}
			if ( $hbg_desc ) {
				echo '<div class="penci-hbg_desc">' . do_shortcode( $hbg_desc ) . '</div>';
			}
			?>
		</div>
		<?php endif; /* Hide hambuger header tag */ ?>
		<?php if( get_theme_mod('penci_menu_hbg_show_search') ): ?>
		<div class="penci-hbg-search-box">
			<form role="search" method="get" id="searchform" class="penci-hbg-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<div class="inner-hbg-search-form">
					<input type="text" class="search-input" placeholder="<?php echo penci_get_setting( 'penci_trans_type_and_hit' ); ?>" name="s" id="s" />
					<i class="fa fa-search"></i>
				</div>
			</form>
		</div>
		<?php endif; ?>
		<div class="penci-hbg-content penci-sidebar-content <?php echo sanitize_text_field( $heading_title . ' ' . $heading_align ); ?>">
			<?php if ( is_active_sidebar( 'menu_hamburger_1' ) ) { ?>
			<div class="penci-menu-hbg-widgets1">
				<?php dynamic_sidebar( 'menu_hamburger_1' ); ?>
			</div>
			<?php } ?>
			<?php
			if ( has_nav_menu( 'main-menu' ) && ! get_theme_mod( 'penci_menu_hbg_hide_menu' ) ) {
				$args = array(
					'container'      => false,
					'theme_location' => 'main-menu',
					'menu_class'     => 'menu menu-hgb-main',
					'fallback_cb'    => 'penci_menu_fallback',
					'walker'         => new penci_menu_walker_nav_menu()
				);
				
				if( get_theme_mod('penci_menu_hbg_primary') ) {
					$custom_menu = get_theme_mod('penci_menu_hbg_primary');
					$args = array(
						'container'      => false,
						'menu' => $custom_menu,
						'menu_class'     => 'menu menu-hgb-main',
						'fallback_cb'    => 'penci_menu_fallback',
						'walker'         => new penci_menu_walker_nav_menu()
					);
				}
				wp_nav_menu( $args );
			}
			?>
			<?php if ( is_active_sidebar( 'menu_hamburger_2' ) ) { ?>
			<div class="penci-menu-hbg-widgets2">
				<?php dynamic_sidebar( 'menu_hamburger_2' ); ?>
			</div>
			<?php } ?>
		</div>
		<div class="penci-hbg-footer">
			<?php
			$footer_text      = get_theme_mod( 'penci_menu_hbg_footer_text' );
			$footer_text 	  = do_shortcode( $footer_text );
			$hide_footer_text = get_theme_mod( 'penci_menu_hbg_hideftext' );

			if( ! $footer_text ) {
				$footer_text = penci_get_setting( 'penci_footer_copyright' );
			}
			if ( $footer_text && ! $hide_footer_text ) {
				echo '<div class="penci_menu_hbg_ftext">';
				echo $footer_text;
				echo '</div>';
			}
			?>
			<?php if ( ! $hide_social ): ?>
				<?php if ( get_theme_mod( 'penci_email_me' ) || get_theme_mod( 'penci_vk' ) || penci_get_setting( 'penci_facebook' ) || penci_get_setting( 'penci_twitter' ) || get_theme_mod( 'penci_google' ) || get_theme_mod( 'penci_instagram' ) || get_theme_mod( 'penci_pinterest' ) || get_theme_mod( 'penci_linkedin' ) || get_theme_mod( 'penci_flickr' ) || get_theme_mod( 'penci_behance' ) || get_theme_mod( 'penci_tumblr' ) || get_theme_mod( 'penci_youtube' ) || get_theme_mod( 'penci_rss' ) ) : ?>
					<div class="header-social sidebar-nav-social penci-hbg-social-<?php echo $social_style; ?>">
						<?php include( trailingslashit( get_template_directory() ) . 'inc/modules/socials.php' ); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

		</div>
	</div>
</div>