<?php

/**
 * @package    Directorypress_Frontend
 * @subpackage Directorypress_Frontend/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Frontend_Admin {
	private $plugin_name;
	private $version;
	
	public function __construct() {
		add_action('directorypress_after_general_settings', array($this, 'settings'), 10, 2);
	}
	public function enqueue_styles() {

	}
	public function enqueue_scripts() {

	}
	
	public function settings($redux, $opt_name) {
		if ( ! class_exists( 'Redux' ) ) {
			return;
		}
		$redux::setSection( $opt_name, array(
			'title' => __( 'DirectoryPress Frontend Addon', 'directorypress-frontend' ),
			'id' => 'directorypress_frontend_settings',
			'icon'  => 'fas fa-tachometer-alt',
		));
		$redux::setSection( $opt_name, array(
			'title' => __( 'Frontend Submission', 'directorypress-frontend' ),
			'subsection' => true,
			'id' => 'front_end_submission_settings',
			'fields' => array(
				array(
					'type' => 'radio',
					'id' => 'directorypress_fsubmit_login_mode',
					'title' => __('Frontend submission login mode', 'directorypress-frontend'),
					'options' => array(
						'1' => __('login required', 'directorypress-frontend'),
						'2' => __('necessary to fill in contact form', 'directorypress-frontend'),
						'3' => __('not necessary to fill in contact form', 'directorypress-frontend'),
						'4' => __('do not show contact form', 'directorypress-frontend'),
					),
					'default' => '1',
				
				),
				array(
						'type' => 'switch',
						'id' => 'directorypress_hide_choose_package_page',
						'title' => __('Hide choose package page', 'directorypress-frontend'),
						'default' => true,
						'desc' => __('When all packages are similar and all has packages of listings, user do not need to choose package to submit when he already has a package.', 'directorypress-frontend'),
					),
				array(
					'type' => 'switch',
					'id' => 'directorypress_fsubmit_moderation',
					'title' => __('Enable pre-moderation of listings', 'directorypress-frontend'),
					'default' => true,
					'desc' => __('Moderation will be required for all listings even after payment', 'directorypress-frontend'),
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_fsubmit_edit_moderation',
					'title' => __('Enable moderation after a listing was modified', 'directorypress-frontend'),
					'default' => true
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_fsubmit_button',
					'title' => __('Enable submit listing button', 'directorypress-frontend'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_change_expiration_date', // needs adjustment
					'title' => __('Allow regular users to change listings expiration dates', 'directorypress-frontend'),
					'default' => false,
				),
				array(
					'type' => 'switch',
					'id' => 'restrict_non_admin',
					'title' => __('Restrict User To Access WordPress Back-end', 'directorypress-frontend'),
					'desc' => __('Restrict all user roles other than Admin to access WordPess Dahboard e.g subscriber, editor,contributor, customer etc', 'directorypress-frontend') .  directorypress_wpml_supported_settings_description(),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_hide_admin_bar',
					'title' => __('Hide top admin bar at the frontend for regular users and do not allow them to see dashboard at all', 'directorypress-frontend'),
					'default' => false,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_allow_edit_profile', // needs adjustment
					'title' => __('Allow users to manage own profile at the frontend dashboard', 'directorypress-frontend'),
					'default' => true,
				),
				array(
					'type' => 'select',
					'id' => directorypress_wpml_supported_option_id('directorypress_tospage'), // adapted for WPML
					'title' => __('Require Terms of Services on submission page?', 'directorypress-frontend'),
					'desc' => __('If yes, create a WordPress page containing your TOS agreement and assign it using the dropdown above.', 'directorypress-frontend') .  directorypress_wpml_supported_settings_description(),
					'data' => 'pages',
					'default' => '', // adapted for WPML
				),
				array(
					'type' => 'select',
					'id' => directorypress_wpml_supported_option_id('directorypress_submit_login_page'), // needs adjustment
					'title' => __('Use custom login page for listings submission process', 'directorypress-frontend'),
					'desc' => __('You may use any 3rd party plugin to make custom login page and assign it with submission process using the dropdown above.', 'directorypress-frontend') .  directorypress_wpml_supported_settings_description(),
					'data' => 'pages',
					'default' => '', // adapted for WPML
				),
				array(
					'type' => 'select',
					'id' => directorypress_wpml_supported_option_id('directorypress_dashboard_login_page'), // adapted for WPML
					'title' => __('Use custom login page for login into dashboard', 'directorypress-frontend'),
					'desc' => __('You may use any 3rd party plugin to make custom login page and assign it with login into dashboard using the dropdown above.', 'directorypress-frontend') .  directorypress_wpml_supported_settings_description(),
					'data' => 'pages',
					'default' => '', // adapted for WPML
				),
				array(
					'type' => 'select',
					'id' => 'directorypress_registration_page', // adapted for WPML // needs adjustment
					'title' => __('Use custom Registration page', 'directorypress-frontend'),
					'desc' => __('Registration link would be redirected to selected page from default login form', 'directorypress-frontend'),
					'data' => 'pages',
					'default' => '', // adapted for WPML
				),
				array(
					'type' => 'select',
					'id' => 'directorypress_password_reset_page', // adapted for WPML // needs adjustment
					'title' => __('Use custom Password Reset Page', 'directorypress-frontend'),
					'desc' => __('Forgot/Reset Password link would be redirected to selected page from default login form', 'directorypress-frontend'),
					'data' => 'pages',
					'default' => '', // adapted for WPML
				),
			
			)
		));
		$redux::setSection( $opt_name, array(
			'title' => __( 'User Profile', 'directorypress-frontend' ),
			'id' => 'front_end_panel_profile',
			'subsection' => true,
			'fields' => array(
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_user_email',
					'title' => __('User Email option on FrontEnd', 'directorypress-frontend'),
					"default" => true,
				),
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_user_phone', // needs adjustment
					'title' => __('User Phone option on FrontEnd', 'directorypress-frontend'),
					"default" => true,
				),
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_user_whatsapp_number', // needs adjustment
					'title' => __('User Whatsapp option on FrontEnd', 'directorypress-frontend'),
					"default" => true,
				),
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_user_address',
					'title' => __('User Address option on FrontEnd', 'directorypress-frontend'),
					"default" => true,
				),
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_user_website',
					'title' => __('User Website option on FrontEnd', 'directorypress-frontend'),
					"default" => true,
				),
				array(
					'type' => 'switch',
					'id' => 'frontend_panel_social_links',
					'title' => __('Social media Links in user panel and frontend', 'directorypress-frontend'),
					'desc' => __('by turning it off social media links will be removed from author page and widgets', 'directorypress-frontend'),
					"default" => true,
				),
			)
		) );
		$redux::setSection( $opt_name, array(
			'title' => __( 'Listing Features', 'directorypress-frontend' ),
			'id' => 'front_end_panel_listing_setting',
			'subsection' => true,
			'fields' => array(
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_stats',
					'title' => __('Enable Performance Static', 'directorypress-frontend'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_note_to_admin',
					'title' => __('Allow user to send custom note to admin', 'directorypress-frontend'),
					"default" => true,
				),
			)
		) );
		$redux::setSection( $opt_name, array(
			'title' => __( 'User Panel Skin', 'directorypress-frontend' ),
			'subsection' => true,
			'id' => 'front_end_panel_skin',
			'fields' => array(
				array(
					'type' => 'switch',
					'id' => 'fup_custom_skin',
					'title' => __('Enable User Panel Styling', 'directorypress-frontend'),
					'desc' => __('In other case claim will be processed immediately without any notifications', 'directorypress-frontend'),
					'default' => false,
				),
				array(
					'id' => 'fup_content_area_background',
					'type' => 'color',
					'title' => esc_html__('Panel Content Area Background', 'directorypress-frontend'),
					'default' => '#f9f9f9',
					//'validate' => 'color',
				),
				array(
					'id' => 'fup_content_area_border_color',
					'type' => 'color',
					'title' => esc_html__('Panel Content Area Border Color', 'directorypress-frontend'),
					'default' => '#eeeeee',
					//'validate' => 'color',
				),
				array(
					'id' => 'fup_sidebar_area_border_color',
					'type' => 'color',
					'title' => esc_html__('Panel Sidebar Area Border Color', 'directorypress-frontend'),
					'default' => '#eeeeee',
					//'validate' => 'color',
				),
				array(
					'id' => 'fup_sidebar_author_section_background',
					'type' => 'color',
					'title' => esc_html__('Sidebar Author Box Background Color', 'directorypress-frontend'),
					'default' => '#f9f9f9',
					'validate' => 'color',
				),
				array(
					'id' => 'fup_sidebar_author_name_color',
					'type' => 'color',
					'title' => esc_html__('Sidebar Author Name Color', 'directorypress-frontend'),
					'default' => '#7b8397',
					'validate' => 'color',
				),
				array(
					'id' => 'fup_sidebar_author_name_status_color',
					'type' => 'color',
					'title' => esc_html__('Sidebar Author Status Color', 'directorypress-frontend'),
					'default' => '#7b8397',
					'validate' => 'color',
				),
				array(
					'id' => 'fup_sidebar_menu_color',
					'type' => 'nav_color',
					'title' => esc_html__('User Panel Menu Color', 'directorypress-frontend'),
					'regular' => true,
					'hover' => true,
					'bg' => true,
					'bg-hover' => true,
					'bg-active' => true,
					'default' => array(
						'regular' => '#888888',
						'hover' => '#777777',
						'bg' => '#ffffff',
						'bg-hover' => '#f3f3f3',
						'bg-active' => '#f3f3f3',
					)
				),
				array(
					'id' => 'fup_sidebar_submenu_color',
					'type' => 'nav_color',
					'title' => esc_html__('User Panel SubMenu Color', 'directorypress-frontend'),
					'regular' => true,
					'hover' => true,
					'bg' => true,
					'bg-hover' => true,
					'bg-active' => true,
					'default' => array(
						'regular' => '#888888',
						'hover' => '#777777',
						'bg' => '#f3f3f3',
						'bg-hover' => '#f3f3f3',
						'bg-active' => '#f3f3f3',
					)
				),
				array(
					'id' => 'fup_sidebar_menu_border_color',
					'type' => 'link_color',
					'title' => esc_html__('User Panel Menu Border Bottom Color', 'directorypress-frontend'),
					'regular' => true,
					'hover' => true,
					'bg' => false,
					'bg-hover' => false,
					'default' => array(
						'regular' => '#eeeeee',
						'hover' => '#eeeeee',
					)
				),
				array(
					'id' => 'fup_sidebar_submenu_border_color',
					'type' => 'link_color',
					'title' => esc_html__('User Panel SubMenu Border Bottom Color', 'directorypress-frontend'),
					'regular' => true,
					'hover' => true,
					'bg' => false,
					'bg-hover' => false,
					'default' => array(
						'regular' => '#cccccc',
						'hover' => '#cccccc',
					)
				),
			)
		) );
	}

}
