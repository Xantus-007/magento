<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Wordpress
 * @since   1.0
 */
get_header();
$logo_url = esc_url( home_url('/') );
$logo_src = get_template_directory_uri() . '/images/logo.png';
if( get_theme_mod( 'penci_logo' ) ) {
	$logo_src = get_theme_mod( 'penci_logo' );
}
/**
 * Set default value if fields is not isset
 *
 */
$image = ! get_theme_mod( 'penci_not_found_image' ) ? get_template_directory_uri() . '/images/404.png' : get_theme_mod( 'penci_not_found_image' );
?>
<div class="headermaintenance">
	<a href="<?php echo esc_url( $logo_url ); ?>">
		<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>">
	</a>
</div>
<div class="errormaintenanceBg">
	<div class="container page-maintenance">
		<div class="error-maintenance">
			<div class="error-image">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/maintenanceimagetop.png" alt="maintenance" />
				<h2>Oups !</h2>
			</div>
			<p class="sub-heading-text-maintenance"><?php echo get_field('maintenance_description', 'options') ?></p>
			<p class="go-back-home"><a target="_blank" href="<?php echo get_field('url_button_maintenance', 'options') ?>"><?php echo get_field('text_button_maintenance', 'options') ?></a></p>
		</div>
	</div>

		
<?php get_footer(); ?>