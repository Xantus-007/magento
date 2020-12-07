<?php
/*
Plugin Name: Penci Review
Plugin URI: http://pencidesign.com/
Description: Review Shortcode Plugin for Soledad theme.
Version: 2.0
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/

/**
 * Include files
 */
require_once( dirname(__FILE__) . '/inc/functions.php' );
require_once( dirname(__FILE__) . '/inc/shortcodes.php' );
require_once( dirname(__FILE__) . '/inc/customize.php' );

/**
 * Add admin meta box style
 */
function penci_load_admin_metabox_review_style() {
	$screen = get_current_screen();
	if ( $screen->id == 'post' ) {
		wp_enqueue_style( 'penci_meta_box_review_styles', plugin_dir_url( __FILE__ ) . 'css/admin-css.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'penci_load_admin_metabox_review_style' );

/* Load Oswald font from selfhosting */
function penci_load_oswald_for_review() {
	if( get_theme_mod( 'penci_disable_default_fonts' ) ) {
?>
	<style type="text/css">@font-face {font-family: 'Oswald';font-style: normal;font-weight: 400;src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752HT8Ghe4.woff2) format('woff2');unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}@font-face {font-family: 'Oswald';font-style: normal;font-weight: 400;src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752Fj8Ghe4.woff2) format('woff2');unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;}@font-face {font-family: 'Oswald';font-style: normal;font-weight: 400;src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752Fz8Ghe4.woff2) format('woff2');unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}@font-face {font-family: 'Oswald';font-style: normal;font-weight: 400;src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752GT8G.woff2) format('woff2');unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }</style>
<?php
	}
}
add_action( 'wp_head', 'penci_load_oswald_for_review' );

/**
 * Add javascript for review plugin
 */
add_action( 'wp_enqueue_scripts', 'penci_register_review_scripts' );

function penci_register_review_scripts(){
	wp_enqueue_script( 'jquery-penci-piechart', plugin_dir_url( __FILE__ ) . 'js/jquery.easypiechart.min.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'jquery-penci-review', plugin_dir_url( __FILE__ ) . 'js/review.js', array('jquery'), '1.0', true );
	if( ! get_theme_mod( 'penci_disable_default_fonts' ) ) {
		wp_enqueue_style('penci-oswald', '//fonts.googleapis.com/css?family=Oswald:400', array(), false, 'all');
	}
}

/**
 * Adds Penci review meta box to the post editing screen
 */
function Penci_Review_Add_Custom_Metabox() {
	new Penci_Review_Add_Custom_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_Review_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_Review_Add_Custom_Metabox' );
}

/**
 * The Class.
 */
class Penci_Review_Add_Custom_Metabox_Class {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array('post');     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'penci_review_meta'
				,esc_html__( 'Add A Review For This Posts', 'soledad' )
				,array( $this, 'render_meta_box_content' )
				,$post_type
				,'advanced'
				,'default'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['penci_review_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['penci_review_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'penci_review_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		// Update the meta field.
		if( isset( $_POST[ 'penci_review_title' ] ) ) {
			update_post_meta( $post_id, 'penci_review_title', $_POST[ 'penci_review_title' ] );
		}
		if( isset( $_POST[ 'penci_review_des' ] ) ) {
			update_post_meta( $post_id, 'penci_review_des', $_POST[ 'penci_review_des' ] );
		}
		if( isset( $_POST[ 'penci_review_1' ] ) ) {
			update_post_meta( $post_id, 'penci_review_1', $_POST[ 'penci_review_1' ] );
		}
		if( isset( $_POST[ 'penci_review_1_num' ] ) ) {
			update_post_meta( $post_id, 'penci_review_1_num', $_POST[ 'penci_review_1_num' ] );
		}
		if( isset( $_POST[ 'penci_review_2' ] ) ) {
			update_post_meta( $post_id, 'penci_review_2', $_POST[ 'penci_review_2' ] );
		}
		if( isset( $_POST[ 'penci_review_2_num' ] ) ) {
			update_post_meta( $post_id, 'penci_review_2_num', $_POST[ 'penci_review_2_num' ] );
		}
		if( isset( $_POST[ 'penci_review_3' ] ) ) {
			update_post_meta( $post_id, 'penci_review_3', $_POST[ 'penci_review_3' ] );
		}
		if( isset( $_POST[ 'penci_review_3_num' ] ) ) {
			update_post_meta( $post_id, 'penci_review_3_num', $_POST[ 'penci_review_3_num' ] );
		}
		if( isset( $_POST[ 'penci_review_4' ] ) ) {
			update_post_meta( $post_id, 'penci_review_4', $_POST[ 'penci_review_4' ] );
		}
		if( isset( $_POST[ 'penci_review_4_num' ] ) ) {
			update_post_meta( $post_id, 'penci_review_4_num', $_POST[ 'penci_review_4_num' ] );
		}
		if( isset( $_POST[ 'penci_review_5' ] ) ) {
			update_post_meta( $post_id, 'penci_review_5', $_POST[ 'penci_review_5' ] );
		}
		if( isset( $_POST[ 'penci_review_5_num' ] ) ) {
			update_post_meta( $post_id, 'penci_review_5_num', $_POST[ 'penci_review_5_num' ] );
		}
		if( isset( $_POST[ 'penci_review_good' ] ) ) {
			update_post_meta( $post_id, 'penci_review_good', $_POST[ 'penci_review_good' ] );
		}
		if( isset( $_POST[ 'penci_review_bad' ] ) ) {
			update_post_meta( $post_id, 'penci_review_bad', $_POST[ 'penci_review_bad' ] );
		}
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'penci_review_custom_box', 'penci_review_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$review_title = get_post_meta( $post->ID, 'penci_review_title', true );
		$review_des = get_post_meta( $post->ID, 'penci_review_des', true );
		$review_1 = get_post_meta( $post->ID, 'penci_review_1', true );
		$review_1num = get_post_meta( $post->ID, 'penci_review_1_num', true );
		$review_2 = get_post_meta( $post->ID, 'penci_review_2', true );
		$review_2num = get_post_meta( $post->ID, 'penci_review_2_num', true );
		$review_3 = get_post_meta( $post->ID, 'penci_review_3', true );
		$review_3num = get_post_meta( $post->ID, 'penci_review_3_num', true );
		$review_4 = get_post_meta( $post->ID, 'penci_review_4', true );
		$review_4num = get_post_meta( $post->ID, 'penci_review_4_num', true );
		$review_5 = get_post_meta( $post->ID, 'penci_review_5', true );
		$review_5num = get_post_meta( $post->ID, 'penci_review_5_num', true );
		$review_good = get_post_meta( $post->ID, 'penci_review_good', true );
		$review_bad = get_post_meta( $post->ID, 'penci_review_bad', true );

		// Display the form, using the current value.
		?>

		<div class="penci-table-meta">
			<h3>Review settings</h3>
			<p>You can display your review for this post by using the following shortcode: <span class="penci-review-shortcode">[penci_review]</span><br>If you do not need this feature, you should go to <strong>Plugins > Installed Plugins > and deactivate plugin "Penci Review"</strong></p>
			<p>
				<label for="penci_review_title" class="penci-format-row">Review Title:</label>
				<input style="width:100%;" type="text" name="penci_review_title" id="penci_review_title" value="<?php if( isset( $review_title ) ): echo $review_title; endif; ?>">
			</p>
			<p>
				<label for="penci_review_des" class="penci-format-row">Description:</label>
				<textarea style="width:100%; height:120px;" name="penci_review_des" id="penci_review_des"><?php if( isset( $review_des ) ): echo $review_des; endif; ?></textarea>
				<span class="penci-recipe-description">You can write some description for your review here.</span>
			</p>
			<p class="review-odd">
				<label for="penci_review_1" class="penci-format-row">Review Title for Point 1:</label>
				<input style="width:100px;" type="text" name="penci_review_1" id="penci_review_1" value="<?php if( isset( $review_1 ) ): echo $review_1; endif; ?>">
				<span class="penci-recipe-description">Example: Design</span>
			</p>
			<p>
				<label for="penci_review_1_num" class="penci-format-row">Review Number for Point 1:</label>
				<input style="width:100px;" type="number" name="penci_review_1_num" id="penci_review_1_num" value="<?php if( isset( $review_1num ) ): echo $review_1num; endif; ?>">
				<span class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
			</p>
			<p class="review-odd">
				<label for="penci_review_2" class="penci-format-row">Review Title for Point 2:</label>
				<input style="width:100px;" type="text" name="penci_review_2" id="penci_review_2" value="<?php if( isset( $review_2 ) ): echo $review_2; endif; ?>">
			</p>
			<p>
				<label for="penci_review_2_num" class="penci-format-row">Review Number for Point 2:</label>
				<input style="width:100px;" type="number" name="penci_review_2_num" id="penci_review_2_num" value="<?php if( isset( $review_2num ) ): echo $review_2num; endif; ?>">
				<span class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
			</p>
			<p class="review-odd">
				<label for="penci_review_3" class="penci-format-row">Review Title for Point 3:</label>
				<input style="width:100px;" type="text" name="penci_review_3" id="penci_review_3" value="<?php if( isset( $review_3 ) ): echo $review_3; endif; ?>">
			</p>
			<p>
				<label for="penci_review_3_num" class="penci-format-row">Review Number for Point 3:</label>
				<input style="width:100px;" type="number" name="penci_review_3_num" id="penci_review_3_num" value="<?php if( isset( $review_3num ) ): echo $review_3num; endif; ?>">
				<span class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
			</p>
			<p class="review-odd">
				<label for="penci_review_4" class="penci-format-row">Review Title for Point 4:</label>
				<input style="width:100px;" type="text" name="penci_review_4" id="penci_review_4" value="<?php if( isset( $review_4 ) ): echo $review_4; endif; ?>">
			</p>
			<p>
				<label for="penci_review_4_num" class="penci-format-row">Review Number for Point 4:</label>
				<input style="width:100px;" type="number" name="penci_review_4_num" id="penci_review_4_num" value="<?php if( isset( $review_4num ) ): echo $review_4num; endif; ?>">
				<span class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
			</p>
			<p class="review-odd">
				<label for="penci_review_5" class="penci-format-row">Review Title for Point 5:</label>
				<input style="width:100px;" type="text" name="penci_review_5" id="penci_review_5" value="<?php if( isset( $review_5 ) ): echo $review_5; endif; ?>">
			</p>
			<p>
				<label for="penci_review_5_num" class="penci-format-row">Review Number for Point 5:</label>
				<input style="width:100px;" type="number" name="penci_review_5_num" id="penci_review_5_num" value="<?php if( isset( $review_5num ) ): echo $review_5num; endif; ?>">
				<span class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
			</p>
			<p>
				<label for="penci_review_good" class="penci-format-row">The Goods:</label>
				<textarea style="width:100%; height:120px;" name="penci_review_good" id="penci_review_good"><?php if( isset( $review_good ) ): echo $review_good; endif; ?></textarea>
				<span class="penci-recipe-description">Type each the good on a new line.</span>
			</p>
			<p>
				<label for="penci_review_bad" class="penci-format-row">The Bads:</label>
				<textarea style="width:100%; height:120px;" name="penci_review_bad" id="penci_review_bad"><?php if( isset( $review_bad ) ): echo $review_bad; endif; ?></textarea>
				<span class="penci-recipe-description">Type each the bad on a new line.</span>
			</p>
		</div>
	<?php
	}
}