<?php
/**
 * Customize for Penci Recipe
 * @since 1.0
 */
function penci_soledad_recipe_customizer( $wp_customize ) {
	// Add Sections
	$wp_customize->add_section( 'penci_new_section_recipe' , array(
		'title'      => 'Recipe Options',
		'description'=> 'Because Google will validate your recipe to make it display good on search results. So, you should set a default value for all default fileds below to make Google validate your recipe bestest. If you want to modify any default field, let go to edit your posts and change recipe data there.',
		'priority'   => 49,
	) );

	// Add settings
	$wp_customize->add_setting( 'penci_recipe_featured_image', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_featured_image', array(
		'label'    => 'Hide Featured Image on Recipe Card',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_featured_image',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_print', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_print', array(
		'label'    => 'Hide Print Button',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_print',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_hide_image_print', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_hide_image_print', array(
		'label'    => 'Hide Images on Recipe Igredients & Instructions When Users Print Recipe',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_hide_image_print',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_nutrition', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_nutrition', array(
		'label'    => 'Show Nutrition Facts',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_nutrition',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_calories', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_calories', array(
		'label'    => 'Hide Calories',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_calories',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_fat', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_fat', array(
		'label'    => 'Hide Fat',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_fat',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	$wp_customize->add_setting( 'penci_recipe_rating', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_rating', array(
		'label'    => 'Hide Rating',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_rating',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );

	$wp_customize->add_setting( 'penci_recipe_ingredients_visual', array(
		'default'           => false,
		'sanitize_callback' => 'penci_recipe_sanitize_checkbox_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_ingredients_visual', array(
		'label'    => 'Make Ingredients is Visual Editor on Edit Recipe Screen',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_ingredients_visual',
		'type'     => 'checkbox',
		'priority' => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfcalories', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfcalories', array(
		'label'       => 'Set default number calories for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfcalories',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dffat', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dffat', array(
		'label'       => 'Set default number fat for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dffat',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfcuisine', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfcuisine', array(
		'label'       => 'Set default recipe cuisine for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfcuisine',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfkeywords', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfkeywords', array(
		'label'       => 'Set default keywords for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfkeywords',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfvideoid', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfvideoid', array(
		'label'       => 'Set default Youtube video ID for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfvideoid',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfvideotitle', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfvideotitle', array(
		'label'       => 'Set default Youtube video title for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfvideotitle',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfvideoduration', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfvideoduration', array(
		'label'       => 'Set default Youtube video duration for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfvideoduration',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfvideodate', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfvideodate', array(
		'label'       => 'Set default Youtube video upload date for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfvideodate',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_dfvideodes', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_dfvideodes', array(
		'label'       => 'Set default Youtube video description for all Recipe Cards',
		'section'     => 'penci_new_section_recipe',
		'settings'    => 'penci_recipe_dfvideodes',
		'description' => '',
		'type'        => 'text',
		'priority'    => 1
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_border_color', array(
		'default'           => '#dedede',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_border_color', array(
		'label'    => 'Recipe Border Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_border_color',
		'priority' => 2
	) ) );

	$wp_customize->add_setting( 'penci_recipe_title_color', array(
		'default'           => '#313131',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_title_color', array(
		'label'    => 'Recipe Title Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_title_color',
		'priority' => 3
	) ) );

	$wp_customize->add_setting( 'penci_recipe_print_button', array(
		'default'           => '#6eb48c',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_print_button', array(
		'label'    => 'Recipe Print Button Accent Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_print_button',
		'priority' => 3
	) ) );

	$wp_customize->add_setting( 'penci_recipe_meta_icon_color', array(
		'default'           => '#aaaaaa',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_meta_icon_color', array(
		'label'    => 'Recipe Meta Icons Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_meta_icon_color',
		'priority' => 4
	) ) );

	$wp_customize->add_setting( 'penci_recipe_meta_color', array(
		'default'           => '#888888',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_meta_color', array(
		'label'    => 'Recipe Meta Text Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_meta_color',
		'priority' => 4
	) ) );

	$wp_customize->add_setting( 'penci_recipe_section_title_color', array(
		'default'           => '#888888',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_section_title_color', array(
		'label'    => 'Recipe Section Title Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_section_title_color',
		'priority' => 5
	) ) );

	$wp_customize->add_setting( 'penci_recipe_note_title_color', array(
		'default'           => '#888888',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_note_title_color', array(
		'label'    => 'Recipe Notes Title Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_note_title_color',
		'priority' => 5
	) ) );

	$wp_customize->add_setting( 'penci_recipe_note_color', array(
		'default'           => '#313131',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_note_color', array(
		'label'    => 'Recipe Notes Text Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_note_color',
		'priority' => 6
	) ) );
	
	$wp_customize->add_setting( 'penci_recipe_index_title_color', array(
		'default'           => '#777777',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_title_color', array(
		'label'    => 'Recipe Index Section Title Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_title_color',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_index_cat_color', array(
		'default'           => '#6eb48c',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_cat_color', array(
		'label'    => 'Recipe Index Posts Categories Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_cat_color',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_index_post_title_color', array(
		'default'           => '#313131',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_post_title_color', array(
		'label'    => 'Recipe Index Posts Title Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_post_title_color',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_index_post_date_color', array(
		'default'           => '#888888',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_post_date_color', array(
		'label'    => 'Recipe Index Posts Date Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_post_date_color',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_index_button_bg', array(
		'default'           => '#313131',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_button_bg', array(
		'label'    => 'Recipe Index Button View All Background Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_button_bg',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_index_button_color', array(
		'default'           => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'recipe_index_button_color', array(
		'label'    => 'Recipe Index Button View All Text Color',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_index_button_color',
		'priority' => 6
	) ) );

	$wp_customize->add_setting( 'penci_recipe_print_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_print_text', array(
		'label'    => 'Custom "Print This" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_print_text',
		'type'     => 'text',
		'priority' => 7
	) ) );

	$wp_customize->add_setting( 'penci_recipe_serves_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_serves_text', array(
		'label'    => 'Custom "Serves" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_serves_text',
		'type'     => 'text',
		'priority' => 8
	) ) );

	$wp_customize->add_setting( 'penci_recipe_prep_time_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_prep_time_text', array(
		'label'    => 'Custom "Prep Time" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_prep_time_text',
		'type'     => 'text',
		'priority' => 9
	) ) );

	$wp_customize->add_setting( 'penci_recipe_cooking_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_cooking_text', array(
		'label'    => 'Custom "Cooking Time" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_cooking_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_nutrition_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_nutrition_text', array(
		'label'    => 'Custom "Nutrition facts:" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_nutrition_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_calories_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_calories_text', array(
		'label'    => 'Custom "calories" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_calories_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_fat_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_fat_text', array(
		'label'    => 'Custom "fat" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_fat_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_rating_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_rating_text', array(
		'label'    => 'Custom "Rating:" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_rating_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_voted_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_voted_text', array(
		'label'    => 'Custom "voted" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_voted_text',
		'type'     => 'text',
		'priority' => 10
	) ) );

	$wp_customize->add_setting( 'penci_recipe_ingredients_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_ingredients_text', array(
		'label'    => 'Custom "INGREDIENTS" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_ingredients_text',
		'type'     => 'text',
		'priority' => 11
	) ) );

	$wp_customize->add_setting( 'penci_recipe_instructions_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_instructions_text', array(
		'label'    => 'Custom "INSTRUCTIONS" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_instructions_text',
		'type'     => 'text',
		'priority' => 12
	) ) );

	$wp_customize->add_setting( 'penci_recipe_notes_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'recipe_notes_text', array(
		'label'    => 'Custom "NOTES" text',
		'section'  => 'penci_new_section_recipe',
		'settings' => 'penci_recipe_notes_text',
		'type'     => 'text',
		'priority' => 13
	) ) );

}
add_action( 'customize_register', 'penci_soledad_recipe_customizer' );

/**
 * Callback function for sanitizing checkbox settings.
 * Use for PenciDesign
 *
 * @param $input
 *
 * @return string default value if type is not exists
 */
function penci_recipe_sanitize_checkbox_field( $input ) {
	if ( $input == 1 ) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Customize colors
 * @since 3.0
 */
function penci_recipe_customizer_css() {
	?>
	<style type="text/css">
		<?php if(get_theme_mod( 'penci_recipe_border_color' )) : ?>.wrapper-penci-recipe .penci-recipe, .wrapper-penci-recipe .penci-recipe-heading, .wrapper-penci-recipe .penci-recipe-ingredients, .wrapper-penci-recipe .penci-recipe-notes { border-color:<?php echo get_theme_mod( 'penci_recipe_border_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_title_color' )) : ?>.post-entry .penci-recipe-heading h2 { color:<?php echo get_theme_mod( 'penci_recipe_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_print_button' )) : ?>.post-entry .penci-recipe-heading a.penci-recipe-print { color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print { border-color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print:hover { background-color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print:hover { color:#fff; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_icon_color' )) : ?>.penci-recipe-heading .penci-recipe-meta span i { color:<?php echo get_theme_mod( 'penci_recipe_meta_icon_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_color' )) : ?>.penci-recipe-heading .penci-recipe-meta { color:<?php echo get_theme_mod( 'penci_recipe_meta_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_section_title_color' )) : ?>.post-entry .penci-recipe-title { color:<?php echo get_theme_mod( 'penci_recipe_section_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_note_title_color' )) : ?>.post-entry .penci-recipe-notes .penci-recipe-title { color:<?php echo get_theme_mod( 'penci_recipe_note_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_note_color' )) : ?>.post-entry .penci-recipe-notes, .post-entry .penci-recipe-notes p { color:<?php echo get_theme_mod( 'penci_recipe_note_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_title_color' )) : ?>.penci-recipe-index-wrap h4.recipe-index-heading { color:<?php echo get_theme_mod( 'penci_recipe_index_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_cat_color' )) : ?>.penci-recipe-index .cat > a.penci-cat-name { color:<?php echo get_theme_mod( 'penci_recipe_index_cat_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_post_title_color' )) : ?>.penci-recipe-index-wrap .penci-recipe-index-title a { color:<?php echo get_theme_mod( 'penci_recipe_index_post_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_post_date_color' )) : ?>.penci-recipe-index .date { color:<?php echo get_theme_mod( 'penci_recipe_index_post_date_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_button_bg' )) : ?>.penci-recipe-index-wrap .penci-index-more-link a { background-color:<?php echo get_theme_mod( 'penci_recipe_index_button_bg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_button_color' )) : ?>.penci-recipe-index-wrap .penci-index-more-link a { color:<?php echo get_theme_mod( 'penci_recipe_index_button_color' ); ?>; }<?php endif; ?>
	</style>
<?php
}
add_action( 'wp_head', 'penci_recipe_customizer_css' );