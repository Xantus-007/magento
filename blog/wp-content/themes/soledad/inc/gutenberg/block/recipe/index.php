<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Recipe' ) ):
class Penci_Gutenberg_Soledad_Recipe {

	public function render( $attributes, $content ) {
		$post_ID = ( isset( $attributes['postID'] ) && $attributes['postID'] ) ? $attributes['postID'] : get_the_ID();
		
		if( ! $post_ID ){
			return esc_html__( 'Empty post id, Enter post Id' );
		}

		if( ! function_exists( 'penci_recipe_shortcode_function' ) ){
			$mess = esc_html__( 'Please active Penci Recipe plugin', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Recipe', $mess ) . '</div>';
		}
		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Recipe', esc_html__( 'Click to edit this block', 'soledad' ) );
		$shortcode =  do_shortcode( '[penci_recipe id="' . $post_ID . '"]' );
		$output .= str_replace( array( "\r\n", "\r", "\n\n", "\n" ), '', $shortcode );
		$output .= '</div><!--endpenci-block-->';

		return $output;
	}
	public function attributes() {
		$post_id = isset( $_GET['post'] ) ? $_GET['post'] : '';

		$options = array(
			'postID' => array(
				'type' => 'string',
				'default' =>  $post_id,
			)
		);

		return $options;
	}
}
endif;