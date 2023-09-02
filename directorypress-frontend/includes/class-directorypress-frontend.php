<?php

/**
 * @package    Directorypress_Frontend
 * @subpackage Directorypress_Frontend/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Frontend {

	protected $loader;
	protected $plugin_name;
	protected $version;
	public $directorytypes = array();
	
	public function __construct() {
		$this->version = DIRECTORYPRESS_FRONTEND_VERSION;
		$this->plugin_name = 'directorypress-frontend';
		global $directorypress_wpml_dependent_options;
		$directorypress_wpml_dependent_options[] = 'directorypress_tospage';
		$directorypress_wpml_dependent_options[] = 'directorypress_submit_login_page';
		$directorypress_wpml_dependent_options[] = 'directorypress_dashboard_login_page';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-frontend-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-frontend-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'constants.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/vc_config.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/general-function.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/listing-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/profile-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/panel-functions.php';
		include_once DPFL_PATH . 'includes/directorypress_class_panel.php';
		include_once DPFL_PATH . 'includes/directorypress_class_submit.php';
		include_once DPFL_PATH . 'includes/directorypress_class_packages_table.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-frontend-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-directorypress-frontend-public.php';
		
		$this->loader = new Directorypress_Frontend_Loader();

	}
	
	private function set_locale() {

		$plugin_i18n = new Directorypress_Frontend_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	
	private function define_admin_hooks() {

		$plugin_admin = new Directorypress_Frontend_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}
	
	private function define_public_hooks() {

		$plugin_public = new Directorypress_Frontend_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}
	
	public function run() {
		$this->loader->run();
		global $directorypress_object, $directorypress_shortcodes_init, $DIRECTORYPRESS_ADIMN_SETTINGS;
		if (!get_option('directorypress_installed_fsubmit'))
			//directorypress_install_fsubmit();
			add_action('init', 'directorypress_install_fsubmit', 0);
		add_action('directorypress_version_upgrade', 'directorypress_upgrade_fsubmit');
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
		// add new shortcodes for frontend submission and dashboard
		$directorypress_shortcodes['directorypress-submit'] = 'directorypress_submit_handler';
		$directorypress_shortcodes['directorypress-dashboard'] = 'directorypress_dashboard_handler';
		$directorypress_shortcodes['directorypress-packages-table'] = 'directorypress_packages_table_handler';
		
		$directorypress_shortcodes_init['directorypress-submit'] = 'directorypress_submit_handler';
		$directorypress_shortcodes_init['directorypress-dashboard'] = 'directorypress_dashboard_handler';
		$directorypress_shortcodes_init['directorypress-packages-table'] = 'directorypress_packages_table_handler';
		
		add_shortcode('directorypress-submit', array($directorypress_object, 'directorypress_shortcode_display'));
		add_shortcode('directorypress-dashboard', array($directorypress_object, 'directorypress_shortcode_display'));
		add_shortcode('directorypress-packages-table', array($directorypress_object, 'directorypress_shortcode_display'));
		
		add_action('init', array($this, 'getSubmitPage'), 0);
		add_action('init', array($this, 'getDasboardPage'), 0);

		add_filter('directorypress_edit_post_url', array($this, 'edit_adverts_links'), 10, 2);

		add_action('directorypress_userpanel_listing_button', array($this, 'userpanel_listing_button'));
		add_action('directorypress_submit_button_dropdown', array($this, 'submit_button_dropdown'), 10, 2);

		add_action('init', array($this, 'remove_admin_bar'));
		if(isset($DIRECTORYPRESS_ADIMN_SETTINGS['restrict_non_admin']) && $DIRECTORYPRESS_ADIMN_SETTINGS['restrict_non_admin']){
			add_action('admin_init', array($this, 'restrict_dashboard'));
		}

		add_action('transition_post_status', array($this, 'on_listing_approval'), 10, 3);
		add_action('directorypress_post_status_on_activation', array($this, 'post_status_on_activation'), 10, 2);
		
		add_filter('no_texturize_shortcodes', array($this, 'directorypress_no_texturize'));

		add_action('dpfl_render_template', array($this, 'check_custom_template'), 10, 2);
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
	}
	public function register_widgets() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/elementor/directorypress-submit.php';
		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new DirectoryPress_Elementor_Submit_Widget() );

	}
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	public function get_loader() {
		return $this->loader;
	}
	
	public function get_version() {
		return $this->version;
	}
	
	public function init() {
		
	}
	
	public function directorypress_no_texturize($shortcodes) {
		$shortcodes[] = 'directorypress-submit';
		$shortcodes[] = 'directorypress-dashboard';

		return $shortcodes;
	}
	
	/**
	 * check is there template in one of these paths:
	 * - themes/theme/directorypress/public/
	 * - plugins/directorypress-frontend/public/
	 * 
	 */
	public function check_custom_template($template, $args) {
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			
			if ($template_path == DPFL_TEMPLATES_PATH && ($dpfl_template = DpFl_Templates($template_file))) {
				return $dpfl_template;
			}
		}
		return $template;
	}

	public function getSubmitPage() {
		global $directorypress_object, $wpdb, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		$directorypress_object->submit_pages_all = array();

		if ($pages = $wpdb->get_results("SELECT ID AS id, post_name AS slug FROM {$wpdb->posts} WHERE (post_content LIKE '%[" . DPFL_SHORTCODE . "]%' OR post_content LIKE '%[" . DPFL_SHORTCODE . " %') AND post_status = 'publish' AND post_type = 'page'", ARRAY_A)) {

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				foreach ($pages AS $key=>&$cpage) {
					if ($tpage = apply_filters('wpml_object_id', $cpage['id'], 'page')) {
						$cpage['id'] = $tpage;
						$cpage['slug'] = get_post($cpage['id'])->post_name;
					} else {
						unset($pages[$key]);
					}
				}
			}
			
			$pages = array_unique($pages, SORT_REGULAR);
			
			$submit_pages = array();
			
			$shortcodes = array(DPFL_SHORTCODE);
			foreach ($pages AS $page_id) {
				$page_id = $page_id['id'];
				$pattern = get_shortcode_regex($shortcodes);
				if (preg_match_all('/'.$pattern.'/s', get_post($page_id)->post_content, $matches) && array_key_exists(2, $matches)) {
					foreach ($matches[2] AS $key=>$shortcode) {
						if (in_array($shortcode, $shortcodes)) {
							if (($attrs = shortcode_parse_atts($matches[3][$key]))) {
								if (isset($attrs['directorytype']) && is_numeric($attrs['directorytype']) && ($directorytype = $directorypress_object->directorytypes->directory_by_id($attrs['directorytype']))) {
									$submit_pages[$directorytype->id]['id'] = $page_id;
									break;
								} elseif (!isset($attrs['id'])) {
									$submit_pages[$directorypress_object->directorytypes->directorypress_get_base_directorytype()->id]['id'] = $page_id;
									break;
								}
							} else {
								$submit_pages[$directorypress_object->directorytypes->directorypress_get_base_directorytype()->id]['id'] = $page_id;
								break;
							}
						}
					}
				}
			}

			foreach ($submit_pages AS &$page) {
				$page_id = $page['id'];
				$page['url'] = get_permalink($page_id);
				$page['slug'] = get_post($page_id)->post_name;
			}
			
			$directorypress_object->submit_pages_all = $submit_pages;
		}
	}

	public function getDasboardPage() {
		global $directorypress_object, $wpdb, $wp_rewrite;
		
		$directorypress_object->dashboard_page_url = '';
		$directorypress_object->dashboard_page_slug = '';
		$directorypress_object->dashboard_page_id = 0;

		if ($dashboard_page = $wpdb->get_row("SELECT ID AS id, post_name AS slug FROM {$wpdb->posts} WHERE post_content LIKE '%[" . DPFL_DASHBOARD_SHORTCODE . "]%' AND post_status = 'publish' AND post_type = 'page' LIMIT 1", ARRAY_A)) {
			$directorypress_object->dashboard_page_id = $dashboard_page['id'];
			$directorypress_object->dashboard_page_slug = $dashboard_page['slug'];
			
			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				if ($tpage = apply_filters('wpml_object_id', $directorypress_object->dashboard_page_id, 'page')) {
					$directorypress_object->dashboard_page_id = $tpage;
					$directorypress_object->dashboard_page_slug = get_post($directorypress_object->dashboard_page_id)->post_name;
				}
			}
			
			if ($wp_rewrite->using_permalinks())
				$directorypress_object->dashboard_page_url = get_permalink($directorypress_object->dashboard_page_id);
			else
				$directorypress_object->dashboard_page_url = add_query_arg('page_id', $directorypress_object->dashboard_page_id, home_url('/'));
		}
	}
	
	public function userpanel_listing_button() {
		
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		if($directorypress_object->directorytypes->isMultiDirectory()){
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_button'] && !empty($directorypress_object->submit_pages_all)) {
				$page_id = get_the_ID();
				
				$submit_pages = array();
				foreach ($directorypress_object->submit_pages_all AS $page) {
					$submit_pages[] = $page['id'];
				}
				
				echo '<li class="">';
					echo '<a class="parent-menu-link" href="#"><i class="fa fa-plus-circle"></i><span>'. __('Submit new', 'directorypress-frontend') .'</span></a>';
					echo '<ul class="submenu">';
				foreach ($directorypress_object->directorytypes->directorypress_array_of_directorytypes AS $directorytype) {
					$href = directorypress_submitUrl(array('directorytype' => $directorytype->id));
						
					$href = apply_filters('directorypress_submit_button_href', $href, $directorytype);
					echo '<li class=""><a href="' . $href . '" rel="nofollow"><span>'. sprintf(__('Submit new %s', 'directorypress-frontend'), $directorytype->single) .'</span></a></li>';
				}
					echo '</ul>';
				echo '</li>';
			}
		}else{
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_button']){
				echo '<li class=""><a href="'. directorypress_submitUrl() .'" rel="nofollow"><i class="fa fa-plus-circle"></i><span>'. __('Submit new listing', 'directorypress-frontend') .'</span></a></li>';
			}
		}
	}
	
	public function submit_button_dropdown($link_text = '', $icon = '') {
		
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		if($directorypress_object->directorytypes->isMultiDirectory()){
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_button'] && !empty($directorypress_object->submit_pages_all)) {
				$page_id = get_the_ID();
				
				$submit_pages = array();
				foreach ($directorypress_object->submit_pages_all AS $page) {
					$submit_pages[] = $page['id'];
				}

				//$directorytypes = $buttons_view->getDirectorytypes();
				foreach ($directorypress_object->directorytypes->directorypress_array_of_directorytypes AS $directory) {
					$this->directorytypes[] = $directory;
				}
				echo '<div class="dropdown directorypress-new-listing-button">';
					if(!empty($link_text) && empty($icon)){
						echo '<a class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><span>'. esc_attr($link_text) .'</span><span class="caret"></span></a>';
					}elseif(empty($link_text) && !empty($icon)){
						echo '<a class="directorypress-submit-btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="'.$icon.'"></i></a>';
					}elseif(!empty($link_text) && !empty($icon)){
						echo '<a class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="'.$icon.'"></i><span>'. esc_attr($link_text) .'</span><span class="caret"></span></a>';
					}
					echo '<ul class="dropdown-menu">';
				foreach ($directorypress_object->directorytypes->directorypress_array_of_directorytypes AS $directorytype) {
					$href = directorypress_submitUrl(array('directorytype' => $directorytype->id));
						
					$href = apply_filters('directorypress_submit_button_href', $href, $directorytype);
					echo '<li class=""><a href="' . $href . '" rel="nofollow"><span>'. sprintf(__('Submit new %s', 'directorypress-frontend'), $directorytype->single) .'</span></a></li>';
				}
					echo '</ul>';
				echo '</div>';
			}
		}else{
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_button']){
				//$icon = 'dgf';
				if(!empty($link_text) && empty($icon)){
					echo '<a class="submit-listing-button-single btn btn-primary" href="'.directorypress_submitUrl().'" rel="nofollow"><span>'. esc_attr($link_text) .'</span></a>';
				}elseif(empty($link_text) && !empty($icon)){
					echo '<a class="submit-listing-button-single" href="'.directorypress_submitUrl().'" rel="nofollow"><i class="'.$icon.'"></i></a>';
				}elseif(!empty($link_text) && !empty($icon)){
					echo '<a class="submit-listing-button-single btn btn-primary" href="'.directorypress_submitUrl().'" rel="nofollow"><i class="'.$icon.'"></i><span>'. esc_attr($link_text) .'</span></a>';
				}
				
			}
		}
	}

	public function add_logout_button() {
		echo '<a class="directorypress-logout-link btn btn-primary" href="' . wp_logout_url(directorypress_directorytype_url()) . '" rel="nofollow" ' . $buttons_view->tooltipMeta(__('Log out', 'directorypress-frontend'), true) . '><span class="glyphicon glyphicon-log-out"></span> ' . ((!$buttons_view->hide_button_text) ? __('Log out', 'directorypress-frontend') : "") . '</a>';
	}
	
	
	public function remove_admin_bar() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_hide_admin_bar']) {
			if (current_user_can('manage_options') || current_user_can('editor')) {
				show_admin_bar(true);
				add_filter('show_admin_bar', '__return_true', 99999);
			}else{
				show_admin_bar(false);
				add_filter('show_admin_bar', '__return_false', 99999);
			}
		}
		
	}

	public function restrict_dashboard() {
		global $directorypress_object, $pagenow;

		if ($pagenow != 'admin-ajax.php' && $pagenow != 'async-upload.php')
			if ((!current_user_can('administrator') && !current_user_can('editor')) && is_admin()) {
				directorypress_add_notification(__('You can not see dashboard!', 'directorypress-frontend'), 'error');
				wp_redirect(directorypress_dashboardUrl());
				die();
			}
	}

	public function edit_adverts_links($url, $post_id) {
		global $directorypress_object;

		if (!is_admin() && $directorypress_object->dashboard_page_url && ($post = get_post($post_id)) && $post->post_type == DIRECTORYPRESS_POST_TYPE)
			return directorypress_dashboardUrl(array('directorypress_action' => 'edit_advert', 'listing_id' => $post_id));
	
		return $url;
	}
	
	public function on_listing_approval($new_status, $old_status, $post) {
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;

		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_approval_notification']) {
			if (
				$post->post_type == DIRECTORYPRESS_POST_TYPE &&
				'publish' == $new_status &&
				'pending' == $old_status &&
				($listing = $directorypress_object->listings_handler_property->init_listing($post)) &&
				($author = get_userdata($listing->post->post_author))
			) {
				$headers[] = "From: " . get_option('blogname') . " <" . directorypress_admin_email() . ">";
				$headers[] = "Reply-To: " . directorypress_admin_email();
				$headers[] = "Content-Type: text/html";
					
				$subject = "[" . get_option('blogname') . "] " . __('Approval of listing', 'directorypress-frontend');
					
				$body = str_replace('[author]', $author->display_name,
						str_replace('[listing]', $listing->post->post_title,
						str_replace('[link]', directorypress_dashboardUrl(),
				$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_approval_notification'])));
					
				wp_mail($author->user_email, $subject, $body, $headers);
			}
		}
	}
	public function post_status_on_activation($status, $listing) {
		$is_moderation = get_post_meta($listing->post->ID, '_requires_moderation', true);
		$is_approved = get_post_meta($listing->post->ID, '_listing_approved', true);
		if (!$is_moderation || ($is_moderation && $is_approved)) {
			return 'publish';
		} elseif ($is_moderation && !$is_approved) {
			return 'pending';
		}
		return $status;
	}
	public function enqueue_scripts_styles($load_scripts_styles = false) {
		
	} 
	
	public function enqueue_login_scripts_styles() {
		global $action;
		$action = 'login';
		do_action('login_enqueue_scripts');
		do_action('login_head');
	}
}
