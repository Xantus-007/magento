<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Review' ) ):
class Penci_Gutenberg_Soledad_Review {

	public function render( $attributes, $content ) {
		$post_ID = ( isset( $attributes['postID'] ) && $attributes['postID'] ) ? $attributes['postID'] : get_the_ID();
		
		if( ! $post_ID ){
			return esc_html__( 'Empty post id, Enter post Id' );
		}

		if( ! function_exists( 'penci_review_shortcode_function' ) ){
			$mess = esc_html__( 'Please active Penci Review plugin', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Review', $mess ) . '</div>';
		}

		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Review', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .= do_shortcode( '[penci_review id="' . $post_ID . '"]' );
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