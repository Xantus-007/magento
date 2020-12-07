<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Latest_Posts' ) ):
class Penci_Gutenberg_Soledad_Latest_Posts {

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
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Latest Posts', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .= do_shortcode( '[latest_posts  ' . $param . ']' );
		$output .= '</div><!--endpenci-block-->';

		return $output;
	}
	public function attributes() {
		$options = array(
			'heading' => array(
				'type' => 'string',
			),
			'style'   => array(
				'type'    => 'string',
				'default' => 'standard',
			),
			'number'  => array(
				'type'    => 'string',
				'default' => '10',
			),
			'paging'  => array(
				'type'    => 'string',
				'default' => 'numbers',
			),
			'morenum' => array(
				'type'    => 'string',
				'default' => '6',
			),
			'exclude' => array(
				'type' => 'string',

			)
		);

		return $options;
	}
}
endif;