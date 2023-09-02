<?php
use TheWebSolver\License_Manager\API\Manager;

class Directorypress_Maps_License_Handler {
	
	const SERVER_URL = 'https://directorypress.co';
	const PARENT_SLUG = 'directorypress-admin-panel';

	public $dirname;
	public $manager;
	private $response;

	public static function init() {
		static $plugin;

		if ( ! is_a( $plugin, get_class() ) ) {
			$plugin = new self();
		}

		return $plugin;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		require_once DIRECTORYPRESS_PATH . 'admin/license/vendor/autoload.php';

		if ( is_admin() ) {
			$this->manager = new Manager( 'directorypress-maps', 'directorypress-maps.php', self::PARENT_SLUG );

			add_action( 'after_setup_theme', array( $this, 'start' ), 5 );
			add_action( 'admin_notices', array( $this, 'show_license_notice' ) );
		}
	}
	public function show_license_notice() {
		$this->manager->show_notice( true );
	}

	public function start() {
		$this->manager
		->validate_with(
			array(
				'license_key' => __( 'Enter a valid license key.', 'directorypress-maps' ),
				'email'       => __( 'Enter valid/same email address used at the time of purchase.', 'directorypress-maps' ),
				'order_id'    => __( 'Enter same/valid purchase order ID.', 'directorypress-maps' ),
				'slug'        => 'wcfm-integration-module',
			)
		)
		->authenticate_with(
			'ck_093706f4c221e7543c7be916218dfd181bcb6f57',
			'cs_02ab8e62e0d5a9199c7d659dc316044ac04972a7',
		)
		->hash_with('39015f264e6fb9de683aaaf4fc3db975159b2d22063e141d7305b407a2d49a0c')
		->connect_with(
			esc_url( self::SERVER_URL ),
			array(
				'timeout'           => 15,
				'namespace'         => 'lmfwc',
				'version'           => 'v2',
				'verify_ssl'        => 2,
				'query_string_auth' => true,
			)
		)
		->disable_form( true );
	}

}
Directorypress_Maps_License_Handler::init();
if ( '' === Directorypress_Maps_License_Handler::PARENT_SLUG ) {
	function add_license_menu() {
		add_options_page(
			__( 'Activate License', 'directorypress-maps' ),
			__( 'Activate License', 'directorypress-maps' ),
			'manage_options',
			Directorypress_Maps_License_Handler::init()->manager->page_slug,
			array( Directorypress_Maps_License_Handler::init()->manager, 'generate_form' )
		);
	}
	add_action( 'admin_menu', 'add_license_menu' );
}