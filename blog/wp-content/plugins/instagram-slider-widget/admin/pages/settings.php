<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The page Settings.
 *
 * @since 1.0.0
 */
class WIS_SettingsPage extends WIS_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'options';

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages420_AdminPage
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Menu icon (only if a page is placed as a main menu).
	 * For example: '~/assets/img/menu-icon.png'
	 * For example dashicons: '\f321'
	 * @var string
	 */
	public $menu_icon;

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-performance';

	/**
	 * Menu position (only if a page is placed as a main menu).
	 * @link http://codex.wordpress.org/Function_Reference/add_menu_page
	 * @var string
	 */
	public $menu_position = 58;

	/**
	 * Menu type. Set it to add the page to the specified type menu.
	 * For example: 'post'
	 * @var string
	 */
	public $menu_post_type = null;

	/**
	 * Visible page title.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $page_title;

	/**
	 * Visible title in menu.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $menu_title;

	/**
	 * If set, an extra sub menu will be created with another title.
	 * @var string
	 */
	public $menu_sub_title;

	/**
	 *
	 * @var
	 */
	public $page_menu_short_description;

	/**
	 * Заголовок страницы, также использует в меню, как название закладки
	 *
	 * @var bool
	 */
	public $show_page_title = true;

	/**
	 * @var int
	 */
	public $page_menu_position = 20;


	/**
	 * @param WIS_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id         = $plugin->getPrefix()."settings";
		$this->page_title = __( 'Settings of Social Slider Widget', 'instagram-slider-widget' );
		$this->menu_title = __( 'Settings', 'instagram-slider-widget' );
		$this->menu_target= $plugin->getPrefix()."widgets-".$plugin->getPluginName();
		$this->menu_icon = '~/admin/assets/img/wis.png';
		$this->capabilitiy = "manage_options";
		$this->template_name = "settings";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	public function assets( $scripts, $styles ) {
		$this->scripts->request( 'jquery' );

		$this->scripts->request( [
			'control.checkbox',
			'control.dropdown'
		], 'bootstrap' );

		$this->styles->request( [
			'bootstrap.core',
			'bootstrap.form-group',
			'bootstrap.separator',
			'control.dropdown',
			'control.checkbox',
		], 'bootstrap' );
	}

	/**
	 * Returns options for the Basic Settings screen.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function getOptions() {

		$options = [];

		$options[] = [
			'type' => 'html',
			'html' => '<h3 style="margin-left:0">General</h3>'
		];

		$options[] = [
			'type' => 'separator'
		];

		/*
		$options[] = [
			'type'    => 'checkbox',
			'way'     => 'buttons',
			'name'    => 'auto-generation',
			'title'   => __( 'Enable automatic post thumbnail generation', 'instagram-slider-widget' ),
			'default' => false,
			'hint'    => __( 'Enable automatic post thumbnail generation', 'instagram-slider-widget' )
		];
		*/

		return $options;
	}

	public function indexAction() {

		// creating a form
		global $form;
		$form = new Wbcr_FactoryForms418_Form( [
			'scope' => substr( $this->plugin->getPrefix(), 0, - 1 ),
			'name'  => 'setting'
		], $this->plugin );

		$form->setProvider( new Wbcr_FactoryForms418_OptionsValueProvider( $this->plugin ) );

		$form->add( $this->getOptions() );

		$wapt_saved = WIS_Plugin::app()->request->post( $this->plugin->getPrefix() . 'saved', '' );
		if ( ! empty( $wapt_saved ) ) {
			$wapt_nonce = WIS_Plugin::app()->request->post( $this->plugin->getPrefix() . 'nonce', '' );
			if ( ! wp_verify_nonce( $wapt_nonce, $this->plugin->getPrefix() . 'settings_form' ) ) {
				wp_die( 'Permission error. You can not edit this page.' );
			}
			$form->save();

			do_action( 'wis/settings/after_form_save' );
		}

		parent::indexAction();
	}
}