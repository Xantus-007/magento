<?php
/**
 * Template to displaying single portfolio
 * This template be registered in this plugin
 *
 * @since 1.0
 */

get_header();
$block_style = get_theme_mod('penci_blockquote_style') ? get_theme_mod('penci_blockquote_style') : 'style-1';
?>

<?php if( ! get_theme_mod( 'penci_disable_breadcrumb' ) ): ?>
	<div class="container penci-breadcrumb single-breadcrumb">
		<span><a class="crumb" href="<?php echo esc_url( home_url('/') ); ?>">
			<?php
			if( function_exists( 'penci_get_setting' ) ) {
				echo penci_get_setting( 'penci_trans_home' );
			} else {
				esc_html_e( 'Home', 'pencidesign' );
			}
			?>
		</a></span><i class="fa fa-angle-right"></i>
		<?php 
		$penci_cats = wp_get_post_terms( get_the_ID(), 'portfolio-category' );
		$wpseo_primary_term = function_exists( 'penci_get_wpseo_primary_term' ) ? penci_get_wpseo_primary_term( 'portfolio-category' ) : '';

		if ( get_theme_mod( 'enable_pri_cat_yoast_seo' ) && $wpseo_primary_term ) {
			echo $wpseo_primary_term;
		}else if ( ! empty( $penci_cats ) ){ ?>
		<span>
			<?php
			echo '<a href="' . get_term_link( $penci_cats[0], 'portfolio-category' ) . '">' . $penci_cats[0]->name . '</a>';
			?>
		</span><i class="fa fa-angle-right"></i>
		<?php } ?>
		<span><?php the_title(); ?></span>
	</div>
<?php endif; ?>

<div class="container <?php if ( get_theme_mod( 'penci_portfolio_single_enable_sidebar' ) ) : ?>penci_sidebar<?php endif; ?>">
	<div id="main">
		<?php /* The loop */
		while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="penci-page-header">
					<h1><?php the_title(); ?></h1>
				</div>
				<div class="post-entry <?php echo 'blockquote-'. $block_style; ?>">
					<div class="portfolio-page-content">
						<?php /* Thumbnail */
						if ( has_post_thumbnail() && ! get_theme_mod( 'penci_portfolio_hide_featured_image_single' ) ) {
							echo '<div class="single-portfolio-thumbnail">';
							the_post_thumbnail( 'penci-full-thumb' );
							echo '</div>';
						}
						?>
						<div class="portfolio-detail">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
				<?php if ( ! get_theme_mod( 'penci_portfolio_share_box' ) ) : ?>
					<div class="tags-share-box hide-tags page-share has-line<?php if( get_theme_mod( 'penci_portfolio_next_prev_project' ) || get_theme_mod( 'penci_portfolio_enable_comment' ) ): echo ' no-border-bottom-portfolio'; endif;?>">
						<div class="post-share">
							<span class="share-title"><?php esc_html_e( 'Share', 'soledad' ); ?></span>
							<div class="list-posts-share">
								<?php
								$facebook_share  = 'https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink();
								$twitter_share   = 'https://twitter.com/home?status=Check%20out%20this%20article:%20' . get_the_title() . '%20-%20' . get_the_permalink();
								$google_share    = 'https://plus.google.com/share?url=' . get_the_permalink();
								$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
								$pinterest_share = 'https://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . $pinterest_image . '&description=' . get_the_title();
								?>
								<a target="_blank" href="<?php echo esc_url( $facebook_share ); ?>"><i class="fa fa-facebook"></i><span class="dt-share"><?php esc_html_e( 'Facebook', 'soledad' ); ?></span></a>
								<a target="_blank" href="<?php echo esc_url( $twitter_share ); ?>"><i class="fa fa-twitter"></i><span class="dt-share"><?php esc_html_e( 'Twitter', 'soledad' ); ?></span></a>
								<a target="_blank" href="<?php echo esc_url( $google_share ); ?>"><i class="fa fa-google-plus"></i><span class="dt-share"><?php esc_html_e( 'Google +', 'soledad' ); ?></span></a>
								<a target="_blank" href="<?php echo esc_url( $pinterest_share ); ?>"><i class="fa fa-pinterest"></i><span class="dt-share"><?php esc_html_e( 'Pinterest', 'soledad' ); ?></span></a>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'penci_portfolio_next_prev_project' ) ) : ?>
					<div class="post-pagination project-pagination">
						<?php
						$next_text = 'Next Project';
						if( get_theme_mod( 'penci_portfolio_next_text' ) ): $next_text = do_shortcode( get_theme_mod( 'penci_portfolio_next_text' ) ); endif;
						$prev_text = 'Previous Project';
						if( get_theme_mod( 'penci_portfolio_prev_text' ) ): $prev_text = do_shortcode( get_theme_mod( 'penci_portfolio_prev_text' ) ); endif;
						?>
						<div class="prev-post">
							<?php previous_post_link('%link', $prev_text, $in_same_term = true, $excluded_terms = '', $taxonomy = 'portfolio-category'); ?>
						</div>
						<div class="next-post">
							<?php next_post_link('%link', $next_text, $in_same_term = true, $excluded_terms = '', $taxonomy = 'portfolio-category'); ?>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( get_theme_mod( 'penci_portfolio_enable_comment' ) ) : ?>
					<?php comments_template( '', true ); ?>
				<?php endif; ?>
				
			</article>
		<?php endwhile; ?>
	</div>

	<?php if ( get_theme_mod( 'penci_portfolio_single_enable_sidebar' ) ) : ?><?php get_sidebar(); ?><?php endif; ?>

<?php get_footer(); ?>