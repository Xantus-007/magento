<?php
/**
 * The Template for displaying all single posts
 *
 * @package Wordpress
 * @since   1.0
 */
get_header();

$single_style = penci_get_single_style();

if ( ! in_array( $single_style, array( 'style-1', 'style-2' ) ) ) {
	get_template_part( 'template-parts/single', $single_style );
	return;
}

$sidebar_enable = penci_single_sidebar_return();
$sidebar_position = penci_get_posts_sidebar_class();
$sidebar_small_width = penci_single_smaller_content_enable();
?>

<?php if( ! get_theme_mod( 'penci_disable_breadcrumb' ) ): ?>
	<?php
	$yoast_breadcrumb = '';
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb single-breadcrumb">', '</div>', false );
	}

	if( $yoast_breadcrumb ){
		echo $yoast_breadcrumb;
	}else{
		$yoast_breadcrumb = '';
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb single-breadcrumb">', '</div>', false );
		}

		if( $yoast_breadcrumb ){
			echo $yoast_breadcrumb;
		 }else{ ?>
		<div class="container penci-breadcrumb single-breadcrumb">
			<span><a class="crumb" href="<?php echo esc_url( home_url('/') ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><i class="fa fa-angle-right"></i>
			<?php
			if ( get_theme_mod( 'enable_pri_cat_yoast_seo' ) ) {
				$primary_term = penci_get_wpseo_primary_term();

				if ( $primary_term ) {
					echo $primary_term;
				} else {
					$penci_cats = get_the_category( get_the_ID() );
					$penci_cat  = array_shift( $penci_cats );
					echo penci_get_category_parents( $penci_cat );
				}
			} else {
				$penci_cats = get_the_category( get_the_ID() );
				$penci_cat  = array_shift( $penci_cats );
				echo penci_get_category_parents( $penci_cat );
			}
			?>
			<span><?php the_title(); ?></span>
		</div>
		<?php } ?>
	<?php } ?>
<?php endif; ?>

<?php if ( 'style-2' == $single_style ) : ?>
	<div class="container container-single<?php if( get_theme_mod( 'penci_home_layout' ) == 'magazine-1' || get_theme_mod( 'penci_home_layout' ) == 'magazine-2' ): ?> container-single-magazine<?php endif;?><?php if( ! $sidebar_enable ): echo ' penci_is_nosidebar'; endif; ?> container-single-fullwidth hentry">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'content', 'single-full' ); ?>
	<?php endwhile; endif; ?>
	</div>
<?php endif; ?>

<div class="container container-single<?php if( get_theme_mod( 'penci_home_layout' ) == 'magazine-1' || get_theme_mod( 'penci_home_layout' ) == 'magazine-2' ): ?> container-single-magazine<?php endif;?><?php if ( $sidebar_enable ) { ?> penci_sidebar <?php echo esc_attr( $sidebar_position ); ?><?php } else { echo ' penci_is_nosidebar'; } ?><?php if( $sidebar_small_width ): ?> penci-single-smaller-width<?php endif; ?><?php if( ! get_theme_mod( 'penci_disable_lightbox_single' ) ): ?> penci-enable-lightbox<?php endif; ?>">
	<div id="main"<?php if ( get_theme_mod( 'penci_sidebar_sticky' ) ): ?> class="penci-main-sticky-sidebar"<?php endif; ?>>
		<div class="theiaStickySidebar">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php /* Count viewed posts */ penci_set_post_views( $post->ID ); ?>
				<?php get_template_part( 'content', 'single' ); ?>
			<?php endwhile; endif; ?>
		</div>
	</div>
	<?php if ( $sidebar_enable ) : ?>
		<?php get_sidebar(); ?>
	<?php endif; ?>

<?php get_footer(); ?>