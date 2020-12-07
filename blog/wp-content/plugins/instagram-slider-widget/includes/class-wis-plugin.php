<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Основной класс плагина Social Slider Widget
 *
 * @author        Artem Prihodko <webtemyk@yandex.ru>
 * @copyright (c) 2019 Webraftic Ltd
 * @version       1.0
 */

class WIS_Plugin extends Wbcr_Factory420_Plugin {

	/**
	 * @see self::app()
	 * @var Wbcr_Factory420_Plugin
	 */
	private static $app;

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * Позволяет разработчику глобально получить доступ к экземпляру класса плагина в любом месте
	 * плагина, но при этом разработчик не может вносить изменения в основной класс плагина.
	 *
	 * Используется для получения настроек плагина, информации о плагине, для доступа к вспомогательным
	 * классам.
	 *
	 * @return Wbcr_Factory420_Plugin
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array  $data
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_path, $data ) {
		parent::__construct( $plugin_path, $data );

		self::$app = $this;

		if ( is_admin() ) {
			// Регистрации класса активации/деактивации плагина
			$this->init_activation();

			// Инициализация скриптов для бэкенда
			$this->admin_scripts();

			//Подключение файла проверки лицензии
			require( WIS_PLUGIN_DIR . '/admin/ajax/check-license.php' );
		}
		else
		{
			$this->front_scripts();
		}

		$this->global_scripts();


	}

	protected function init_activation() {
		include_once( WIS_PLUGIN_DIR . '/admin/class-wis-activation.php' );
		$this->registerActivation( 'WIS_Activation' );
	}

	/**
	 * Регистрирует классы страниц в плагине
	 */
	private function register_pages() {
//		require_once WIS_PLUGIN_DIR . '/admin/class-wis-page.php';
//		self::app()->registerPage( 'WIS_WidgetsPage', WIS_PLUGIN_DIR . '/admin/pages/widgets.php' );
//		self::app()->registerPage( 'WIS_SettingsPage', WIS_PLUGIN_DIR . '/admin/pages/settings.php' );
//		self::app()->registerPage( 'WIS_LicensePage', WIS_PLUGIN_DIR . '/admin/pages/license.php' );
//		self::app()->registerPage( 'WIS_AboutPage', WIS_PLUGIN_DIR . '/admin/pages/about.php' );
	}

	/**
	 * Код для админки
	 */
	private function admin_scripts()
	{
		// Регистрация страниц
		$this->register_pages();

		add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_assets'] );
	}

	/**
	 * Код для админки и фронтенда
	 */
	private function global_scripts() {

	}

	/**
	 * Код для админки и фронтенда
	 */
	private function front_scripts() {
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_assets'] );
	}

	public function admin_enqueue_assets()
	{
		wp_enqueue_style( 'jr-insta-admin-styles', WIS_PLUGIN_URL.'/admin/assets/css/jr-insta-admin.css', array(), WIS_PLUGIN_VERSION );
		wp_enqueue_script( 'jr-insta-admin-script', WIS_PLUGIN_URL.'/admin/assets/js/jr-insta-admin.js',  array( 'jquery' ), WIS_PLUGIN_VERSION, true );
	}

	public function enqueue_assets()
	{
		wp_enqueue_style( 'instag-slider', WIS_PLUGIN_URL.'/assets/css/instag-slider.css', array(), WIS_PLUGIN_VERSION );
		wp_enqueue_script( 'jquery-pllexi-slider', WIS_PLUGIN_URL.'/assets/js/jquery.flexslider-min.js', array( 'jquery' ), '2.2', false );
	}
}