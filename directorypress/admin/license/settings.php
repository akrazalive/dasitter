<?php

/**
 * ILC Tabbed Settings Page
 */

add_action( 'init', 'ilc_admin_init' );
add_action( 'admin_menu', 'ilc_settings_page_init' );

function ilc_admin_init() {
	$settings = get_option( "ilc_theme_settings" );
	if ( empty( $settings ) ) {
		$settings = array(
			'ilc_intro' => 'Some intro text for the home page',
			'ilc_tag_class' => false,
			'ilc_ga' => false
		);
		add_option( "ilc_theme_settings", $settings, '', 'yes' );
	}	
}

function ilc_settings_page_init() {
	//$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );
	$settings_page = add_theme_page( 'sample menu', 'sample menu'. ' Theme Settings', 'edit_theme_options', 'theme-settings', 'ilc_settings_page' );
	add_action( "load-{$settings_page}", 'ilc_load_settings_page' );
}

function ilc_load_settings_page() {
	if ( $_POST["ilc-settings-submit"] == 'Y' ) {
		check_admin_referer( "ilc-settings-page" );
		ilc_save_theme_settings();
		$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
		wp_redirect(admin_url('themes.php?page=theme-settings&'.$url_parameters));
		exit;
	}
}

function ilc_save_theme_settings() {
	global $pagenow;
	$settings = get_option( "ilc_theme_settings" );
	
	if ( $pagenow == 'themes.php' && $_GET['page'] == 'theme-settings' ){ 
		if ( isset ( $_GET['tab'] ) )
	        $tab = $_GET['tab']; 
	    else
	        $tab = 'homepage'; 

	    switch ( $tab ){ 
	        case 'general' :
				$settings['ilc_tag_class']	  = $_POST['ilc_tag_class'];
			break; 
	        case 'footer' : 
				$settings['ilc_ga']  = $_POST['ilc_ga'];
			break;
			case 'homepage' : 
				$settings['ilc_intro']	  = $_POST['ilc_intro'];
			break;
	    }
	}
	
	if( !current_user_can( 'unfiltered_html' ) ){
		if ( $settings['ilc_ga']  )
			$settings['ilc_ga'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['ilc_ga'] ) ) );
		if ( $settings['ilc_intro'] )
			$settings['ilc_intro'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['ilc_intro'] ) ) );
	}

	$updated = update_option( "ilc_theme_settings", $settings );
}

function ilc_admin_tabs( $current = 'homepage' ) { 
    $tabs = array( 'homepage' => 'Home', 'general' => 'General', 'footer' => 'Footer' ); 
    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=theme-settings&tab=$tab'>$name</a>";
        
    }
    echo '</h2>';
}

function ilc_settings_page() {
	global $pagenow;
	$settings = get_option( "ilc_theme_settings" );
	//$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );
	?>
	
	<div class="wrap">
		<h2><?php echo $theme_data['Name']; ?> Theme Settings</h2>
		
		<?php
			if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
			
			if ( isset ( $_GET['tab'] ) ) ilc_admin_tabs($_GET['tab']); else ilc_admin_tabs('homepage');
		?>

		<div id="poststuff">
			<form method="post" action="<?php admin_url( 'themes.php?page=theme-settings' ); ?>">
				<?php
				wp_nonce_field( "ilc-settings-page" ); 
				
				if ( $pagenow == 'themes.php' && $_GET['page'] == 'theme-settings' ){ 
				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'homepage'; 
					
					echo '<table class="form-table">';
					switch ( $tab ){
						case 'general' :
							?>
							<tr>
								<th><label for="ilc_tag_class">Tags with CSS classes:</label></th>
								<td>
									<input id="ilc_tag_class" name="ilc_tag_class" type="checkbox" <?php if ( $settings["ilc_tag_class"] ) echo 'checked="checked"'; ?> value="true" /> 
									<span class="description">Output each post tag with a specific CSS class using its slug.</span>
								</td>
							</tr>
							<?php
						break; 
						case 'footer' : 
							?>
							<tr>
								<th><label for="ilc_ga">Insert tracking code:</label></th>
								<td>
									<textarea id="ilc_ga" name="ilc_ga" cols="60" rows="5"><?php echo esc_html( stripslashes( $settings["ilc_ga"] ) ); ?></textarea><br/>
									<span class="description">Enter your Google Analytics tracking code:</span>
								</td>
							</tr>
							<?php
						break;
						case 'homepage' : 
							?>
							<tr>
								<th><label for="ilc_intro">Introduction</label></th>
								<td>
									<textarea id="ilc_intro" name="ilc_intro" cols="60" rows="5" ><?php echo esc_html( stripslashes( $settings["ilc_intro"] ) ); ?></textarea><br/>
									<span class="description">Enter the introductory text for the home page:</span>
								</td>
							</tr>
							<?php
						break;
					}
					echo '</table>';
				}
				?>
				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
					<input type="hidden" name="ilc-settings-submit" value="Y" />
				</p>
			</form>
			
			<p><?php echo $theme_data['Name'] ?> theme by <a href="http://ilovecolors.com.ar/">ilovecolors.com.ar</a> | <a href="http://twitter.com/eliorivero">Follow me on Twitter</a>! | Join <a href="http://on.fb.me/ilcfb">ilovecolors on Facebook</a>!</p>
		</div>

	</div>
<?php
}


?>