<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Popular_Posts' ) ):
class Penci_Gutenberg_Soledad_Popular_Posts {

	public function render( $attributes, $content ) {
		$param = ' wpblock="true"';
		if( $attributes ){
			foreach ( (array)$attributes as $k => $v ){
				if( $v ){
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}

		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Popular Posts', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .= do_shortcode( '[popular_posts  ' . $param . ']' );
		$output .= '</div><!--endpenci-block-->';

		return $output;
	}

	public function attributes() {
		$options = array(
			'title'    => array(
				'type'    => 'string',
				'default' => esc_html__( 'Popular Posts', 'soledad' ),
			),
			'type'     => array(
				'type'    => 'string',
				'default' => 'all',
			),
			'category' => array(
				'type' => 'string',
			),
			'number'   => array(
				'type'    => 'string',
				'default' => '12',
			),
			'columns'  => array(
				'type'    => 'string',
				'default' => '4',
			)
		);

		return $options;
	}
}
endif;