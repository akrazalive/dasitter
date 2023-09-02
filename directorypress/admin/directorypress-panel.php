<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DirectoryPress_Admin_Panel {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Redirect to welcome page
		if ( isset( $_GET['page'] ) ) {
			if ($_GET['page'] == 'directorypress-admin-panel' || substr( $_GET['page'], 0, 15 ) == "directorypress_" || $_GET['page'] == 'directorypress_settings') {
				add_action( 'admin_footer', array( $this, 'quick_access' ) );
			}
		}
	}
	
	static function dashboard_menu() {
		global $submenu;

		$menus			= $submenu['directorypress-admin-panel'];
		$menu_size		= sizeof( $menus );
		$menu			= '';
		$crt_pg_name	= get_admin_page_title();
		$base			= explode( 'directorypress', get_current_screen()->base);
		$base			= 'directorypress' . $base[1];
		foreach ($menus as $sub_menu ) {
			$acive_page = ( $base == $sub_menu[2] ) ? ' nav-tab-active' : '' ;
			$menu .= '<a class="nav-tab' . $acive_page . '" href="' . esc_url( self_admin_url( 'admin.php?page='.$sub_menu[2] ) ) . '">' . esc_html( $sub_menu[0], 'DIRECTORYPRESS' ) . '</a>';
		}
		echo $menu;
	}
	
	static function listing_dashboard_header() {
		echo '<div class="directorypress-admin-header wp-clearfix">';
			echo '<div class="directorypress-admin-logo"></div>';
			echo '<div class="directorypress-admin-version">'.esc_html__( 'V', 'DIRECTORYPRESS' ).' '.DIRECTORYPRESS_VERSION.'</div>';
		echo '</div>';
		echo '<div class="directorypress-admin-header-after"></div>';
		echo '<div class="nav-tab-wrapper wp-clearfix">';
			DirectoryPress_Admin_Panel::dashboard_menu();
		echo '</div>';
		
	}
	public function enqueue_scripts() {
		 if ( isset( $_GET['page'] ) ) :
			if ($_GET['page'] == 'directorypress-admin-panel' || substr( $_GET['page'], 0, 15 ) == "directorypress_" || $_GET['page'] == 'directorypress_settings') :
				wp_enqueue_style('bootstrap');
				wp_enqueue_style('fontawesome');
				wp_enqueue_style( 'directorypress-admin-panel-styles', DIRECTORYPRESS_URL . 'admin/assets/css/directorypress-panel.css', 99 );
				wp_enqueue_style('directorypress-select2');
				wp_enqueue_script('directorypress-select2');
				wp_enqueue_script('directorypress-select2-triger');
				wp_enqueue_script('directorypress_admin_script');
			endif; // substr
		endif; // isset 
	}

	public function admin_menus() {
		
			add_menu_page(
				esc_html__( 'DirectoryPress', 'DIRECTORYPRESS' ),
				esc_html__( 'DirectoryPress', 'DIRECTORYPRESS' ),
				'manage_options',
				'directorypress-admin-panel',
				array($this, 'screen_welcome'),
				'',
				15
			);
			/* call_user_func_array( 'add' . '_sub' . 'menu_' . 'page', array(
				'directorypress-admin-panel',
				esc_html__( 'Settings', 'DIRECTORYPRESS' ),
				esc_html__( 'Settings', 'DIRECTORYPRESS' ),
				'manage_options',
				'directorypress_admin_settings',
				array($this, 'generate_panel')
			)); */
	}
	
	public function screen_welcome() {
		echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
		do_action('directorypress_dashboad_panel');
	}
	public function generate_panel() {
		//global $redux_object;
		//require_once DIRECTORYPRESS_PATH . 'admin/ReduxCore/framework.php';
		//require_once DIRECTORYPRESS_PATH . 'admin/ReduxCore/directorypress-settings.php';
		ReduxFramework::init();
        do_action( 'redux/init' );
		
    }
	public function quick_access() {
		
		$current_scr 	= get_current_screen();
		$current_page	= $current_scr->id;
		$protocol		= is_ssl() ? 'https://' : 'http://';
		?>
		
		<div class="directorypress-qucik-help-wrapper">
			<div class="directorypress-qucik-help-icon">
				<i class="fas fa-info"></i>
			</div>
			<ul class="directorypress-qucik-help-content">
				<?php

				switch ($current_page) {
					case 'directorypress_page_directorypress_locations_depths': ?>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/direcotypress/directorypress-fields/how-to-create-new-fileds/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Create New Filed', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<?php
						break;
					case 'directorypress_page_directorypress_fields': ?>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/directorypress/directorypress-fields/how-to-create-new-fileds/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Create New Filed', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/directorypress/directorypress-fields/how-to-edit-filed/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Edit Filed', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<?php
						break;
					case 'directorypress_page_directorypress_directorytypes': ?>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/direcotypress/directorypress-fields/how-to-create-new-fileds/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Create New Filed', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<?php
						break;
					case 'directorypress_page_directorypress_packages': ?>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/direcotypress/directorypress-fields/how-to-create-new-fileds/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Create New Package', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<?php
						break;
					
					default: ?>
						<li>
							<a href="<?php echo $protocol . 'directorypress.co/docs/direcotypress/directorypress-fields/how-to-create-new-fileds/'; ?>" target="_blank" >
								<?php esc_html_e( 'How To Create New Filed', 'DIRECTORYPRESS' ); ?>
							</a>
						</li>
						<?php
						break;
				} ?>
			</ul>
		</div>
	<?php
	}
}
new DirectoryPress_Admin_Panel();