<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Recipe_Index' ) ):
class Penci_Gutenberg_Soledad_Recipe_Index {

	public function render( $attributes, $content ) {
		if( ! function_exists( 'penci_recipe_index_function' ) ){
			$mess = esc_html__( 'Please active Penci Recipe plugin', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Recipe', $mess ) . '</div>';
		}

		$param = ' wpblock="true"';
		if( $attributes ){
			foreach ( (array)$attributes as $k => $v ){
				if( in_array( $k , array( 'display_title','display_cat','display_date','display_image','cat_link' ) ) ){
					$param .= ' ' . $k . '="' . ( $v ? 'yes' : 'no' ) . '"';
				}elseif( $v ){
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}
		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Recipe Index', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .=  do_shortcode( '[penci_index' . $param . ']' );
		$output .= '</div><!--endpenci-block-->';
		return $output;
	}
	public function attributes() {
		$options = array(
			'title'         => array(
				'type'    => 'string',
				'default' => esc_html__( 'Recipe Index Title', 'soledad' ),
			),
			'cat'           => array(
				'type'    => 'string',
				'default' => '',
			),
			'numbers_posts' => array(
				'type'    => 'number',
				'default' => '3',
			),
			'columns'       => array(
				'type'    => 'string',
				'default' => '3',
			),
			'display_title' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'display_cat'   => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'display_date'  => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'display_image' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'image_size'    => array(
				'type'    => 'string',
				'default' => 'landscape',
			),
			'cat_link'      => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'cat_link_text' => array(
				'type'    => 'string',
				'default' => esc_html__( 'View All', 'soledad' ),
			),
		);

		return $options;
	}
}
endif;