<?php
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Don't run the shortcode inside dashboard */
if( is_admin() ){
	return;
}

function penci_recipe_get_post_category( $id ) {
	$cat_return = 'Uncategorized';
	$the_category = get_the_category( $id );
	
	if( ! empty( $the_category ) ){
		$cat_return = $the_category[0]->name;
	}
	
	if( class_exists( 'WPSEO_Primary_Term' ) ){
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term               = get_term( $wpseo_primary_term );
		if ( ! is_wp_error( $term ) ) {
			$cat_return = $term->name;
		}
	}
	
	return $cat_return;
}

/**
 * Penci Recipe Shortcode
 * Use penci_recipe to display the recipe on single a post
 */
function penci_recipe_shortcode_function( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );

	$recipe_id = get_the_ID();
	if ( ! empty( $id ) && is_numeric( $id ) ) {
		$recipe_id = $id;
	}

	// Get recipe meta
	$recipe_title        = get_post_meta( $recipe_id, 'penci_recipe_title', true );
	$recipe_servings     = get_post_meta( $recipe_id, 'penci_recipe_servings', true );
	$recipe_cooktime     = get_post_meta( $recipe_id, 'penci_recipe_cooktime', true );
	$recipe_cooktime_fm  = get_post_meta( $recipe_id, 'penci_recipe_cooktime_format', true );
	$recipe_preptime     = get_post_meta( $recipe_id, 'penci_recipe_preptime', true );
	$recipe_preptime_fm  = get_post_meta( $recipe_id, 'penci_recipe_preptime_format', true );
	$recipe_ingredients  = get_post_meta( $recipe_id, 'penci_recipe_ingredients', true );
	$recipe_instructions = get_post_meta( $recipe_id, 'penci_recipe_instructions', true );
	$recipe_note         = get_post_meta( $recipe_id, 'penci_recipe_note', true );
	
	$recipe_calories = get_post_meta( $recipe_id, 'penci_recipe_calories', true ) ? get_post_meta( $recipe_id, 'penci_recipe_calories', true ) : get_theme_mod('penci_recipe_dfcalories');
	$recipe_fat = get_post_meta( $recipe_id, 'penci_recipe_fat', true ) ? get_post_meta( $recipe_id, 'penci_recipe_fat', true ) : get_theme_mod('penci_recipe_dffat');
	$recipe_keywords = get_post_meta( $recipe_id, 'penci_recipe_keywords', true ) ? get_post_meta( $recipe_id, 'penci_recipe_keywords', true ) : get_theme_mod('penci_recipe_dfkeywords');
	$recipe_cuisine = get_post_meta( $recipe_id, 'penci_recipe_cuisine', true ) ? get_post_meta( $recipe_id, 'penci_recipe_cuisine', true ) : get_theme_mod('penci_recipe_dfcuisine');
	$recipe_videoid = get_post_meta( $recipe_id, 'penci_recipe_videoid', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videoid', true ) : get_theme_mod('penci_recipe_dfvideoid');
	$recipe_videotitle = get_post_meta( $recipe_id, 'penci_recipe_videotitle', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videotitle', true ) : get_theme_mod('penci_recipe_dfvideotitle');
	$recipe_videoduration = get_post_meta( $recipe_id, 'penci_recipe_videoduration', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videoduration', true ) : get_theme_mod('penci_recipe_dfvideoduration');
	$recipe_videodate = get_post_meta( $recipe_id, 'penci_recipe_videodate', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videodate', true ) : get_theme_mod('penci_recipe_dfvideodate');
	$recipe_videodes = get_post_meta( $recipe_id, 'penci_recipe_videodes', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videodes', true ) : get_theme_mod('penci_recipe_dfvideodes');
	
	$recipe_calories = $recipe_calories ? $recipe_calories : '200';
	$recipe_fat = $recipe_fat ? $recipe_fat : '20 grams';
	
	if( ! metadata_exists('post', $recipe_id, 'penci_recipe_rate_total') ){
		add_post_meta( $recipe_id, 'penci_recipe_rate_total', '5' );
	}
	if( ! metadata_exists('post', $recipe_id, 'penci_recipe_rate_people') ){
		add_post_meta( $recipe_id, 'penci_recipe_rate_people', '1' );
	}

	$rate_total          = get_post_meta( $recipe_id, 'penci_recipe_rate_total', true );
	$rate_people         = get_post_meta( $recipe_id, 'penci_recipe_rate_people', true );

	// Turn ingredients into an array
	$recipe_ingredients_array = '';
	if( $recipe_ingredients ):
	$recipe_ingredients_trim = wp_strip_all_tags( $recipe_ingredients );
	$recipe_ingredients_array = preg_split( '/\r\n|[\r\n]/', $recipe_ingredients_trim );
	endif;

	// Rate number
	$rate_number = 5;
	if( $rate_total && $rate_people ){
		$rate_number = number_format( intval( $rate_total ) / intval( $rate_people ), 1 );
	}
	$allow_rate = 1;
	if( isset( $_COOKIE[ 'recipe_rate_postid_'.$recipe_id ] ) ){
		$allow_rate = 0;
	}

	$rand = rand(100, 9999);
	wp_enqueue_script('jquery-recipe-print');
	$excerpt = get_the_excerpt() ? get_the_excerpt() : get_the_title();
	
	$thumb_alt = $thumb_title_html = '';
	if( has_post_thumbnail( $recipe_id ) && function_exists( 'penci_get_image_alt' ) && function_exists( 'penci_get_image_title' ) ){
		$thumb_id = get_post_thumbnail_id( $recipe_id );
		$thumb_alt = penci_get_image_alt( $thumb_id, $recipe_id );
		$thumb_title_html = penci_get_image_title( $thumb_id );
	}
	ob_start(); ?>
	
	<div class="wrapper-penci-recipe">
		<div class="penci-recipe<?php if ( ! has_post_thumbnail( $recipe_id ) || get_theme_mod('penci_recipe_featured_image') ): echo ' penci-recipe-hide-featured'; endif; ?><?php if( get_theme_mod('penci_recipe_hide_image_print') ): echo ' penci-hide-images-print'; endif;?>" id="printrepcipe<?php echo $rand; ?>" itemscope itemtype="http://schema.org/Recipe">
			<div class="penci-recipe-heading">
				
				<?php if ( has_post_thumbnail( $recipe_id ) ): ?>
					<div class="penci-recipe-thumb<?php if( get_theme_mod( 'penci_recipe_featured_image' ) ): echo ' penci-hide-tagupdated'; endif; ?>">
						<img itemprop="image" src="<?php the_post_thumbnail_url(); ?>" alt="<?php echo $thumb_alt; ?>"<?php echo $thumb_title_html; ?> width="150" height="150" />
					</div>
				<?php endif; ?>
				
				<div class="penci-recipe-metades">
					<?php if ( $recipe_title ) : ?>
						<h2 itemprop="name"><?php echo $recipe_title; ?></h2>
					<?php endif; ?>

					<span itemprop="author" class="penci-hide-tagupdated"><?php global $post; $author_id = $post->post_author; the_author_meta( 'nickname', $author_id ); ?></span>
					<span itemprop="description" class="penci-hide-tagupdated"><?php echo $excerpt; ?></span>
					<span itemprop="recipeCategory" class="penci-hide-tagupdated"><?php echo penci_recipe_get_post_category( $recipe_id ); ?></span>
					<span itemprop="keywords" class="penci-hide-tagupdated"><?php if( $recipe_keywords ){ echo $recipe_keywords; } else { echo wp_strip_all_tags( get_the_title() ); } ?></span>
					<span itemprop="recipeCuisine" class="penci-hide-tagupdated"><?php if( $recipe_cuisine ){ echo $recipe_cuisine; } else { echo 'European'; } ?></span>
					<?php if( $recipe_videoid && $recipe_videotitle && $recipe_videoduration && $recipe_videodate && $recipe_videodes ): ?>
						<div itemprop="video" class="penci-hide-tagupdated" itemscope itemtype="http://schema.org/VideoObject">
							<span itemprop="name"><?php echo $recipe_videotitle; ?></span>
							<meta itemprop="duration" content="PT<?php echo $recipe_videoduration; ?>" />
							<meta itemprop="uploadDate" content="<?php echo $recipe_videodate; ?>"/>
							<meta itemprop="thumbnailURL" content="https://img.youtube.com/vi/<?php echo $recipe_videoid; ?>/hqdefault.jpg" />
							<meta itemprop="embedURL" content="https://youtube.googleapis.com/v/<?php echo $recipe_videoid; ?>" />
							<div id="schema-videoobject" class="video-container">
								<iframe width="853" height="480" src="https://www.youtube.com/embed/<?php echo $recipe_videoid; ?>?rel=0&controls=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
							</div>
							<span itemprop="description"><?php echo $recipe_videodes; ?></span>
						</div>
					<?php endif; ?>
					
					<?php if ( ! get_theme_mod( 'penci_recipe_print' ) ) : ?>
						<a href="#" class="penci-recipe-print" data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=2.2'; ?>"><i class="fa fa-print"></i> <?php if( get_theme_mod( 'penci_recipe_print_text' ) ) { esc_html_e( 'Print This', 'soledad' ); } else { esc_html_e( 'Print This', 'soledad' ); } ?></a>
					<?php endif; ?>

					<?php if ( $recipe_servings || $recipe_cooktime || $recipe_preptime ) : ?>
						<div class="penci-recipe-meta">
							<?php if ( $recipe_servings ) : ?><span>
								<i class="pers-ic"></i> <?php if( get_theme_mod( 'penci_recipe_serves_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_serves_text' ) ); } else { esc_html_e( 'Serves', 'soledad' ); } ?>: <span class="servings" itemprop="recipeYield"><?php echo
									$recipe_servings; ?></span>
								</span>
							<?php endif; ?>
							<?php if ( $recipe_preptime ) : ?>
								<span>
								<i class="fa fa-clock-o"></i> <?php if( get_theme_mod( 'penci_recipe_prep_time_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_prep_time_text' ) ); } else { esc_html_e( 'Prep Time', 'soledad' ); } ?>: <time <?php if( $recipe_preptime_fm ): echo 'datetime="PT'. $recipe_preptime_fm .'" '; endif;?>itemprop="prepTime"><?php echo $recipe_preptime; ?></time>
								</span>
							<?php endif; ?>
							<?php if ( $recipe_cooktime ) : ?>
								<span>
								<i class="cook-ic"></i> <?php if( get_theme_mod( 'penci_recipe_cooking_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_cooking_text' ) ); } else { esc_html_e( 'Cooking Time', 'soledad' ); } ?>: <time <?php if( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm .'" '; endif;?>itemprop="totalTime"><?php echo $recipe_cooktime; ?></time>
								<time class="penci-hide-tagupdated" <?php if( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm .'" '; endif;?>itemprop="cookTime"><?php echo $recipe_cooktime; ?></time>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					
					<div itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation" class="penci-recipe-rating penci-nutrition<?php if( get_theme_mod( 'penci_recipe_nutrition' ) ): echo ' penci-show-nutrition'; endif; ?>">
						<span class="nutrition-lable"><?php if( get_theme_mod( 'penci_recipe_nutrition_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_nutrition_text' ) ); } else { esc_html_e( 'Nutrition facts:', 'soledad' ); } ?></span>
						<span itemprop="calories" class="nutrition-item<?php if( get_theme_mod( 'penci_recipe_calories' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_calories . ' '; if( get_theme_mod( 'penci_recipe_calories_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_calories_text' ) ); } else { esc_html_e( 'calories', 'soledad' ); } ?></span>
						<span itemprop="fatContent" class="nutrition-item<?php if( get_theme_mod( 'penci_recipe_fat' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_fat . ' '; if( get_theme_mod( 'penci_recipe_fat_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_fat_text' ) ); } else { esc_html_e( 'fat', 'soledad' ); } ?></span>
					</div>

					<?php if ( ! get_theme_mod( 'penci_recipe_rating' ) ) : ?>
						<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="penci-recipe-rating penci-recipe-review">
							<span class="penci-rate-text">
								<?php echo __( 'Rating', 'soledad' ).': '; ?>
								<!-- <span itemprop="ratingValue" class="penci-rate-number"><?php echo $rate_number; ?></span>/5 -->
							</span>
							<div class="penci_rateyo" id="penci_rateyo" data-allow="<?php esc_attr_e( $allow_rate )?>" data-rate="<?php esc_attr_e( $rate_number );?>" data-postid="<?php esc_attr_e( $recipe_id );?>" data-people="<?php echo $rate_people; ?>" data-total="<?php echo $rate_total; ?>"></div>
							<span class="penci-numbers-rate">( <span class="penci-number-people" itemprop="reviewCount"><?php echo $rate_people; ?></span> <?php if( get_theme_mod( 'penci_recipe_voted_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_voted_text' ) ); } else {esc_html_e( 'voted', 'soledad' ); } ?> )</span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $recipe_ingredients ) : ?>
				<div class="penci-recipe-ingredients<?php if( get_theme_mod( 'penci_recipe_ingredients_visual' ) ){ echo ' penci-recipe-ingre-visual'; } else { echo ' penci-recipe-not-visual'; } ?>">
					<h3 class="penci-recipe-title"><?php if( get_theme_mod( 'penci_recipe_ingredients_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_ingredients_text' ) ); } else { esc_html_e( 'Ingredients', 'soledad' ); } ?></h3>
					<?php if( ! get_theme_mod( 'penci_recipe_ingredients_visual' ) ){ ?>
					<ul>
						<?php foreach ( $recipe_ingredients_array as $ingredient ) : ?>
							<?php if ( $ingredient ) :
							$ing_trim = trim($ingredient);
							$str_ing = substr( $ing_trim ,0,2);
							$str_echo = substr( $ing_trim , 2);
							if( $str_ing == '==' ){
							?>
								<h4 class="recipe-ingredient-heading"><span itemprop="recipeIngredient"><?php echo $str_echo; ?></span></h4>
							<?php } else { ?>
								<li><span itemprop="recipeIngredient"><?php echo $ingredient; ?></span></li>
							<?php } endif; ?>
						<?php endforeach; ?>
					</ul>
					<?php } else { ?>
						<?php
						//echo apply_filters( 'the_content', htmlspecialchars_decode( $recipe_ingredients ) );
						$content_autop = wpautop( do_shortcode( htmlspecialchars_decode( $recipe_ingredients ) ) );
						$content_autop1 = str_replace( '<p', '<p itemprop="recipeIngredient"', $content_autop );
						$content_autop2 = str_replace( '<li', '<li itemprop="recipeIngredient"', $content_autop1 );
						echo $content_autop2;
						?>
					<?php } ?>
				</div>
			<?php endif; ?>

			<?php if ( $recipe_instructions ) : ?>
				<div class="penci-recipe-method" itemprop="recipeInstructions">
					<h3 class="penci-recipe-title"><?php if( get_theme_mod( 'penci_recipe_instructions_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_instructions_text' ) ); } else { esc_html_e( 'Instructions', 'soledad' ); } ?></h3>
					<?php
					echo wpautop( do_shortcode( htmlspecialchars_decode( $recipe_instructions ) ) );
					?>
				</div>
			<?php endif; ?>

			<?php if ( $recipe_note ) : ?>
				<div class="penci-recipe-notes">
					<h3 class="penci-recipe-title"><?php if( get_theme_mod( 'penci_recipe_notes_text' ) ) { echo do_shortcode( get_theme_mod( 'penci_recipe_notes_text' ) ); } else { esc_html_e( 'Notes', 'soledad' ); } ?></h3>
					<p><?php echo $recipe_note; ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'penci_recipe', 'penci_recipe_shortcode_function' );


/**
 * Penci Recipe Index
 *
 * Use penci_index to display the recipe on single a post
 */
function penci_recipe_index_function( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title'         => '',
		'cat'           => '',
		'numbers_posts' => '',
		'columns'       => '',
		'display_title' => 'yes',
		'display_cat'   => 'no',
		'display_date'  => 'yes',
		'display_image' => 'yes',
		'image_size'    => 'square',
		'cat_link'      => 'yes',
		'cat_link_text' => 'View All'
	), $atts ) );

	$index_title = isset( $title ) ? $title : '';
	$index_cat = isset( $cat ) ? $cat : '';
	$index_numbers_posts = isset( $numbers_posts ) ? $numbers_posts : '3';
	$index_cols = isset( $columns ) ? $columns : '3';
	$index_display_title = isset( $display_title ) ? $display_title : 'yes';
	$index_display_cat = isset( $display_cat ) ? $display_cat : 'no';
	$index_display_date = isset( $display_date ) ? $display_date : 'yes';
	$index_display_image = isset( $display_image ) ? $display_image : 'yes';
	$index_image_size = isset( $image_size ) ? $image_size : 'square';
	$index_cat_link = isset( $cat_link ) ? $cat_link : 'yes';
	$index_cat_text = isset( $cat_link_text ) ? $cat_link_text : 'View All';

	$index_query = new WP_Query( array(
		'category_name' => $index_cat,
		'posts_per_page' => $index_numbers_posts,
		'ignore_sticky_posts' => true
	) );
	
	$post_found = $index_query->found_posts;

	ob_start();

	$cat_link = '';
	$open_link = '';
	$close_link = '';
	if($index_cat) :
		$index_cat = do_shortcode( $index_cat );
		$catOj = get_category_by_slug($index_cat);
		$cat_id = $catOj->term_id;
		$cat_link = get_category_link( $cat_id );
	endif;

	if ( $index_cat_link == "yes" && $cat_link ):
		$open_link = '<a href="'. esc_url( $cat_link ) .'">';
		$close_link = '</a>';
	endif;
	?>

	<?php if ( $index_query->have_posts() ) : ?>
	<div class="penci-recipe-index-wrap">
		<?php if ( $index_title ) : ?>
			<h4 class="recipe-index-heading"><span><?php echo $open_link. do_shortcode( $index_title ) . $close_link; ?></span></h4>
		<?php endif; ?>

		<?php
		/* Define columns of recipe index */
		$columns_class = '3';
		if( $index_cols == '2' || $index_cols == '4' ) {
			$columns_class = $index_cols;
		}
		?>
		<ul class="penci-recipe-index column-<?php echo $columns_class; ?>">
			<?php while ( $index_query->have_posts() ) : $index_query->the_post(); ?>
				<li>
					<article id="post-<?php the_ID(); ?>" <?php post_class('penci-recipe-item'); ?>>
						<?php if ( $index_display_image != 'no' && function_exists( 'penci_get_featured_image_size' ) ) : ?>
							<div class="penci-index-post-img">
								<?php $thumbnail_size = 'penci-thumb-square';
								if( $index_image_size == 'vertical' ) {
									$thumbnail_size = 'penci-thumb-vertical';
								} elseif( $index_image_size == 'horizontal' ) {
									$thumbnail_size = 'penci-thumb';
								}
								?>
								<?php if( ! get_theme_mod( 'penci_disable_lazyload_layout' ) ) { ?>
								<a href="<?php the_permalink(); ?>" class="penci-image-holder penci-holder-size-<?php echo $index_image_size; ?> penci-lazy" data-src="<?php echo penci_get_featured_image_size( get_the_ID(), $thumbnail_size ); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
								<?php } else { ?>
								<a href="<?php the_permalink(); ?>" class="penci-image-holder penci-holder-size-<?php echo $index_image_size; ?>" style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), $thumbnail_size ); ?>');" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
								<?php } ?>
							</div>
						<?php endif; /* End check for thumbnails */ ?>

						<?php if($index_display_cat == 'yes') : ?>
							<span class="cat"><?php penci_category( '' ); ?></span>
						<?php endif; ?>

						<?php if($index_display_title != 'no') : ?>
							<h2 class="penci-recipe-index-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php endif; ?>

						<?php if($index_display_date != 'no') : ?>
							<span class="date"><?php the_time( get_option('date_format') ); ?></span>
						<?php endif; ?>
					</article>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php if ( $index_cat_link == "yes" && $cat_link && ( $post_found > $index_numbers_posts ) ) : ?>
			<div class="penci-index-more-link"><a href="<?php echo esc_url( $cat_link ); ?>"><?php echo do_shortcode( $index_cat_text ); ?> <i class="fa fa-long-arrow-right"></i></a></div>
		<?php endif; ?>

	</div>
	<?php wp_reset_postdata(); ?>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

add_shortcode( 'penci_index', 'penci_recipe_index_function' );
