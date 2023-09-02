<?php

/**
 * @package    Directorypress_Frontend
 * @subpackage Directorypress_Frontend/public
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Frontend_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {
		//wp_enqueue_style( 'submit-css', DPFL_URL . 'assets/css/submit.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, DPFL_URL . 'assets/css/directorypress-frontend-public.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, DPFL_URL . 'assets/js/directorypress-frontend-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-profile', DPFL_URL . 'assets/js/directorypress-frontend-profile.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-listing', DPFL_URL . 'assets/js/directorypress-frontend-listing.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'chart-js', DPFL_URL . 'assets/js/chart.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'dpfl_custom_vars', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			//'dashboard_listing' => DPFL_URL.'public/adverts.tpl.php',
		));
	}

}
