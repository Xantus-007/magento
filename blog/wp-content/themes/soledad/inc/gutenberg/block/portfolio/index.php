<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Portfolio' ) ):
class Penci_Gutenberg_Soledad_Portfolio {

	public function render( $attributes, $content ) {

		if( ! function_exists( 'penci_portfolio_shortcode' ) ){
			$mess = esc_html__( 'Please active Penci Portfolio plugin', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Portfolio', $mess ) . '</div>';
		}

		$param = ' wpblock="true"';
		if( $attributes ){
			foreach ( (array)$attributes as $k => $v ){
				if( $v ){
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}
		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Portfolio', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .=  do_shortcode( '[portfolio ' . $param . ']' );
		$output .= '</div><!--endpenci-block-->';

		return $output;
	}
	public function attributes() {
		$options = array(
			'style'      => array(
				'type'    => 'string',
				'default' => 'masonry',
			),
			'cat'        => array(
				'type' => 'string',
			),
			'number'     => array(
				'type'    => 'number',
				'default' => '15',
			),
			'pagination' => array(
				'type'    => 'string',
				'default' => 'number',
			),
			'numbermore' => array(
				'type'    => 'number',
				'default' => '6',
			),
			'image_type' => array(
				'type'    => 'string',
				'default' => 'landscape',
			),
			'filter'     => array(
				'type'    => 'string',
				'default' => 'true',
			),
			'column'     => array(
				'type'    => 'number',
				'default' => '3',
			),
			'all_text'   => array(
				'type'    => 'string',
				'default' => esc_html__( 'All', 'soledad' ),
			),
		);

		return $options;
	}
}
endif;