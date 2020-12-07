<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package    WordPress
 * @subpackage Soledad Theme
 * @since      1.0
 */
get_header(); ?>

<?php if( get_theme_mod( 'penci_home_adsense_below_slider' ) ): ?>
	<div class="container penci-adsense-below-slider">
		<?php echo do_shortcode( get_theme_mod( 'penci_home_adsense_below_slider' ) ); ?>
	</div>
<?php endif; ?>

<?php
if ( ! get_theme_mod( 'penci_home_hide_boxes' ) && ( get_theme_mod( 'penci_home_box_img1' ) || get_theme_mod( 'penci_home_box_img2' ) || get_theme_mod( 'penci_home_box_img3' ) || get_theme_mod( 'penci_home_box_img4' ) ) ):
	get_template_part( 'inc/modules/home_boxes' );
endif;

/* Homepage Popular Post */
if( get_theme_mod( 'penci_enable_home_popular_posts' ) ) {
	get_template_part( 'inc/modules/home_popular' );
}

/* Home layout */
$layout_this = get_theme_mod( "penci_home_layout" );
$sidebar_position = 'right-sidebar';
if( get_theme_mod( "penci_left_sidebar_home" ) ) { $sidebar_position = 'left-sidebar'; }

if ( ! isset( $layout_this ) || empty( $layout_this ) ): $layout_this = 'standard'; endif;
$class_layout = '';
if( $layout_this == 'classic' ): $class_layout = ' classic-layout'; endif;
?>

	<div class="container<?php echo esc_attr( $class_layout ); if ( penci_get_setting( 'penci_sidebar_home' ) ) : ?> penci_sidebar <?php echo esc_attr( $sidebar_position ); ?><?php endif; ?>">
		<div id="main" class="penci-layout-<?php echo esc_attr( $layout_this ); ?><?php if ( get_theme_mod( 'penci_sidebar_sticky' ) ): ?><?php endif; ?>">
			<div class="theiaStickySidebar">

				<?php
				/**
				 * Featured categories for magazine layouts
				 *
				 * @since 1.0
				 */
				if( ! get_theme_mod( 'penci_move_latest_posts_above' ) && ( ( get_theme_mod( 'penci_home_featured_cat' ) && ( $layout_this == 'magazine-1' || $layout_this == 'magazine-2' ) ) || get_theme_mod( 'penci_enable_featured_cat_all_layouts' ) ) ):
					get_template_part( 'inc/modules/featured-categories' );
				endif;
				?>

				<?php if( ! get_theme_mod( 'penci_hide_latest_post_homepage' ) ): ?>

					<?php
					$heading_widget_title = get_theme_mod( 'penci_sidebar_heading_style' ) ? get_theme_mod( 'penci_sidebar_heading_style' ) : 'style-1';
					$heading_widget_align = get_theme_mod( 'penci_sidebar_heading_align' ) ? get_theme_mod( 'penci_sidebar_heading_align' ) : 'pcalign-center';
					$heading_title = get_theme_mod( 'penci_featured_cat_style' ) ? get_theme_mod( 'penci_featured_cat_style' ) : $heading_widget_title;
					$heading_align = get_theme_mod( 'penci_heading_latest_align' ) ? get_theme_mod( 'penci_heading_latest_align' ) : $heading_widget_align;
					?>

					<?php if ( get_theme_mod( 'penci_home_title' ) ) : ?>
						<div class="penci-border-arrow penci-homepage-title penci-home-latest-posts <?php echo sanitize_text_field( $heading_title . ' ' . $heading_align ); ?>">
							<h3 class="inner-arrow"><?php echo penci_get_setting( 'penci_home_title' ); ?></h3>
						</div>
					<?php endif; ?>

					<div class="penci-wrapper-posts-content">
						<h2 class="RecTitle"><?php echo esc_html__('Latest recipes','soledad');  ?></h2>
						<?php if( in_array( $layout_this, array( 'standard', 'classic', 'overlay' ) ) ): ?><div class="penci-wrapper-data"><?php endif; ?>
						<?php if ( in_array( $layout_this, array( 'mixed', 'mixed-2', 'overlay-grid', 'overlay-list', 'photography', 'grid', 'grid-2', 'list', 'boxed-1', 'boxed-2', 'standard-grid', 'standard-grid-2', 'standard-list', 'standard-boxed-1', 'classic-grid', 'classic-grid-2', 'classic-list', 'classic-boxed-1', 'magazine-1', 'magazine-2' ) ) ) : ?><ul class="penci-wrapper-data penci-grid"><?php endif; ?>
						<?php if( in_array( $layout_this, array( 'masonry', 'masonry-2' ) ) ) : ?><div class="penci-wrap-masonry"><div class="penci-wrapper-data masonry penci-masonry"><?php endif; ?>

						<?php /* The loop */
						if (have_posts()) :
						while ( have_posts() ) : the_post();
							include( locate_template( 'content-' . $layout_this . '.php' ) );
						endwhile;
						?>

						<?php if( in_array( $layout_this, array( 'masonry', 'masonry-2' ) ) ) : ?></div></div><?php endif; ?>
						<?php if ( in_array( $layout_this, array( 'mixed', 'mixed-2', 'overlay-grid', 'overlay-list', 'photography', 'grid', 'grid-2', 'list', 'boxed-1', 'boxed-2', 'standard-grid', 'standard-grid-2', 'standard-list', 'standard-boxed-1', 'classic-grid', 'classic-grid-2', 'classic-list', 'classic-boxed-1', 'magazine-1', 'magazine-2' ) ) ) : ?></ul><?php endif; ?>
						<?php if( in_array( $layout_this, array( 'standard', 'classic', 'overlay' ) ) ): ?></div><?php endif; ?>

						<?php if( get_theme_mod( 'penci_page_navigation_ajax' ) || get_theme_mod( 'penci_page_navigation_scroll' ) ) { ?>
							<?php
							$button_class = 'penci-ajax-more penci-ajax-home penci-ajax-more-click';
							if( get_theme_mod( 'penci_page_navigation_scroll' ) ):
								$button_class = 'penci-ajax-more penci-ajax-home penci-ajax-more-scroll';
							endif;
							/* Get data template */
							$data_layout = $layout_this;
							if ( in_array( $layout_this, array( 'standard-grid', 'classic-grid', 'overlay-grid' ) ) ) {
								$data_layout = 'grid';
							} elseif ( in_array( $layout_this, array( 'standard-grid-2', 'classic-grid-2' ) ) ) {
								$data_layout = 'grid-2';
							} elseif ( in_array( $layout_this, array( 'standard-list', 'classic-list', 'overlay-list' ) ) ) {
								$data_layout = 'list';
							} elseif ( in_array( $layout_this, array( 'standard-boxed-1', 'classic-boxed-1' ) ) ) {
								$data_layout = 'boxed-1';
							}

							$data_template = 'sidebar';

							if( ! penci_get_setting( 'penci_sidebar_home' ) ):
							$data_template = 'no-sidebar';
							endif;

							/* Get data offset */
							$offset_number = get_option('posts_per_page');
							if( get_theme_mod( 'penci_home_lastest_posts_numbers' ) && 0 != get_theme_mod( 'penci_home_lastest_posts_numbers' ) ):
								$offset_number = get_theme_mod( 'penci_home_lastest_posts_numbers' );
							endif;
							$num_load = 6;
							if( get_theme_mod( 'penci_number_load_more' ) && 0 != get_theme_mod( 'penci_number_load_more' ) ):
								$num_load = get_theme_mod( 'penci_number_load_more' );
							endif;
							?>
							<div class="penci-pagination <?php echo $button_class; ?>">
								<a class="penci-ajax-more-button" data-mes="<?php echo penci_get_setting('penci_trans_no_more_posts'); ?>" data-layout="<?php echo esc_attr( $data_layout ); ?>" data-number="<?php echo absint($num_load); ?>" data-offset="<?php echo absint($offset_number); ?>"
								   data-from="customize" data-template="<?php echo $data_template; ?>">
									<span class="ajax-more-text"><?php echo penci_get_setting('penci_trans_load_more_posts'); ?></span><span class="ajaxdot"></span><i class="fa fa-refresh"></i>
								</a>
							</div>
						<?php } else { ?>
						<?php penci_soledad_pagination(); ?>
						<?php } ?>

					</div>

					<?php endif; wp_reset_postdata(); /* End if of the loop */ ?>

				<?php endif; /* End check if not hide latest on homepage */ ?>

				<?php
				/**
				 * Featured categories for magazine layouts
				 *
				 * @since 1.0
				 */
				if( get_theme_mod( 'penci_move_latest_posts_above' ) && ( ( get_theme_mod( 'penci_home_featured_cat' ) && ( $layout_this == 'magazine-1' || $layout_this == 'magazine-2' ) ) || get_theme_mod( 'penci_enable_featured_cat_all_layouts' ) ) ):
					get_template_part( 'inc/modules/featured-categories' );
				endif;
				?>
			</div>
		</div>

		<?php if ( penci_get_setting( 'penci_sidebar_home' ) ) : ?>
			<?php get_sidebar(); ?>
		<?php endif; 
		
		$args = array(  
					'post_type' => 'post',
					'post_status' => 'publish',
					'meta_query' => array(
						array(
							'key' => 'penci_recipe_videoid',
							'value'   => '',
							'compare' => '!='
						)
					)
				);
				
				$loop = new WP_Query( $args );
		if($loop->post_count > 0){
		?>
		<div class="penci_sidebar penci-single-style-10 YoutubeVideosCOntainer">
			<h2 class="RecTitle"><?php echo esc_html__('Video recipes','soledad');  ?></h2>
			<div class="penci-single-s10-content">
			<?php
			
				// echo DEVELOPER_KEY;
				
				$url="";
				while ( $loop->have_posts() ) : $loop->the_post();
				// $youtubeURL = get_field('video_url');
				// $arr_result = getVideoInformation($youtubeURL);
				$youtubeURL = get_post_meta( get_the_ID(), 'penci_recipe_videoid', true );
				$arr_result = getVideoInformation( $youtubeURL );
				$yt = $arr_result->items[0]; 
				$imageResolutiion = get_theme_mod('youtubeImageSize');
			?>
			<div id="videoIframe">
				<img src="<?php echo $yt->snippet->thumbnails->{"$imageResolutiion"}->url; ?>" data-videoId="<?php echo $yt->id; ?>" />
				<img src="<?php echo get_stylesheet_directory_uri().'/images/youtube-1.png'; ?>" class="playbutton" alt="youtube-play" />

				<div id="videoIframeInner">

				</div>
			</div>
			<?php 
					break;
				endwhile;
				wp_reset_postdata();
			?>
			</div>
			<div class="penci-sidebar-content style-4 pcalign-center">
			<?php
			
				// $args = array(  
				// 	'post_type' => 'VideosYoutube',
				// 	'post_status' => 'publish',
				// );
				$args = array(  
					'post_type' => 'post',
					'post_status' => 'publish',
					'meta_query' => array(
						array(
							'key' => 'penci_recipe_videoid',
							'value'   => '',
							'compare' => '!='
						)
					)
				);

				$loop = new WP_Query( $args );
				$url="";
				while ( $loop->have_posts() ) : $loop->the_post();

					// $youtubeURL = get_field('video_url');
					// $arr_result = getVideoInformation($youtubeURL ,true);
					$youtubeURL = get_post_meta( get_the_ID(), 'penci_recipe_videoid', true );
					$arr_result = getVideoInformation( $youtubeURL );
			        $yt = $arr_result->items[0]; 
					$imageResolutiion = get_theme_mod( 'youtubeImageSize' );
					
			?>
				<div class="videoGal" data-videoId="<?php echo $yt->id; ?>" >
					<img src="<?php echo $yt->snippet->thumbnails->{"$imageResolutiion"}->url; ?>" />
					<div class="rightInfo">
						<a href="<?php the_permalink(); ?>" class="VideoTitle"><?php the_title(); ?></a>
						<span class="VideoViews"><?php echo  number_format($yt->statistics->viewCount,0, '', ' ').' '.__('views', 'soledad'); ?> </span>
					</div>
				</div>

			<?php 

				endwhile;
				wp_reset_postdata();
			?>
			</div>
		</div>
		<?php 
		}
		?>
	</div>
		<?php
			$img1 = get_theme_mod('penci_home_box_img1');
			$img2 = get_theme_mod('penci_home_box_img2');
			if( !empty($img1) && !empty($img2)  )
			{
				$link1 = get_theme_mod('penci_home_box_url1');
				$link2 = get_theme_mod('penci_home_box_url2');
		?>
			<div class="home-featured-cat-content HomeBoxes">
				<div class="container">
					<div class="row">
						<div class="cat-left">
							<a target="_blank" href="<?php echo $link1; ?>">
								<img src="<?php echo $img1; ?>" />
							</a>
						</div>
						<div class="cat-right">
							<a target="_blank" href="<?php echo $link2; ?>">
								<img src="<?php echo $img2; ?>" />
							</a>
						</div>
					</div>
				</div>
			</div>
		<?php		
			}
			?>
<div class="container">
<?php get_footer(); ?>