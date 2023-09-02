<?php

class directorypress_pmpro {

	public function __construct() {
		//add_action('init', array($this, 'pmpro_packages_array'));
		add_filter('pmpro_get_membership_package_for_user', array($this, 'check_package_price'));
		add_action('pmpro_checkout_after_package_cost', array($this, 'package_description'));
		add_action('pmpro_member_action_links_after', array($this, 'buy_more_link'));
		add_action('pmpro_membership_package_after_other_settings', array($this, 'package_settings'));
		add_action('pmpro_save_membership_package', array($this, 'save_package_settings'), 10, 1);
		add_action('pmpro_after_membership_package_profile_fields', array($this, 'user_profile_fields'), 10, 1);
		add_filter("pmpro_pages_shortcode_packages", array($this, 'pages_shortcode_packages'));
		add_filter("wp", array($this, 'scripts_styles'));
	}
	
	public function check_package_price($package) {
		//var_dump($package);
		
		return $package;
	}
	
	/* public function pmpro_packages_array() {
		global $current_user;
		
		//var_dump($current_user->membership_package);
	} */
	
	public function buy_more_link() {
		global $current_user;
		?>
		<a style="font-size: 1.5em;" href="<?php echo pmpro_url("checkout", "?package=" . $current_user->membership_package->id . "&buymore=1", "https")?>"><?php _e("Buy more listings", "directorypress-frontend");?></a>
		<?php 
	}
	
	public function package_description() {
		global $directorypress_object, $pmpro_package;

		?>
		<table id="directorypress_directory_listings" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th><?php _e('Directory listings available', 'directorypress-frontend'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					<?php foreach ($directorypress_object->packages->packages_array as $package): ?>
						<div>
							<label><?php echo $package->name; ?>:</label>
							<label><?php echo getPMPROlistingsNumberByLevel($pmpro_package->id, $package->id); ?></label>
						</div>
					<?php endforeach; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php 
	}

	public function package_settings() {
		global $directorypress_object, $wpdb;

		if (isset($_REQUEST['edit']))
			$edit = $_REQUEST['edit'];	
		else
			$edit = false;

		$directorypress_pmpro_packages = get_option('directorypress_pmpro_packages');
		?>
		<script>
		jQuery(document).ready(function($) {
			$("input[name*='directorypress_package_unlimited_']").each( function() {
				packageUnlimitedChange($(this));
			});

			$("input[name*='directorypress_package_unlimited_']").change( function() {
				packageUnlimitedChange($(this));
			});

			function packageUnlimitedChange(checkbox) {
				if (checkbox.is(':checked'))
					checkbox.parent().parent().find(".directorypress_package_value").attr('disabled', 'true');
				else
					checkbox.parent().parent().find(".directorypress_package_value").removeAttr('disabled');
			}
		});
		</script>
		<h3 class="topborder"><?php _e('Directory Settings', 'directorypress-frontend');?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label><?php _e('Listings packages', 'directorypress-frontend');?>:</label></th>
					<td>
						<?php
						if ($edit) {
							$pmpro_package = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_packages WHERE id = '$edit' LIMIT 1", OBJECT);
							$pmpro_package_id = $pmpro_package->id;
						} else 
							$pmpro_package_id = 0;

						echo "<ul>";
						foreach ($directorypress_object->packages->packages_array as $package) {
							echo "<li>";
							echo "<span>";
							echo "<input name='directorypress_package_{$package->id}' class='directorypress_package_value' type='text' size='2' value='".(isset($directorypress_pmpro_packages[$pmpro_package_id][$package->id]) ? $directorypress_pmpro_packages[$pmpro_package_id][$package->id]['value'] : 0)."' />";
							_e(' or ', 'directorypress-frontend');
							echo "<label><input name='directorypress_package_unlimited_{$package->id}' type='checkbox' class='unlimited_listings' value='1' ".(isset($directorypress_pmpro_packages[$pmpro_package_id][$package->id]) ? checked($directorypress_pmpro_packages[$pmpro_package_id][$package->id]['unlimited'], 1, false) : 'checked="checked"')." />" . __('unlimited', 'directorypress-frontend') . '</label> ';
							echo "- {$package->name}";
							echo "</span>";
							echo "</li>";
						}
						echo "</ul>";
						?>
						<small><?php _e('Enter number of allowed directorytype listings for this membership package or set unlimited.', 'directorypress-frontend');?></small>
					</td>
				</tr>
			</tbody>
		</table>
		<?php 
	}
	
	public function save_package_settings($saveid) {
		global $directorypress_object, $msg, $msgt;
		
		$validation = new directorypress_form_validation();
		foreach ($directorypress_object->packages->packages_array as $package) {
			$validation->set_rules('directorypress_package_'.$package->id, __('Listings number', 'directorypress-frontend'), 'numeric');
			$validation->set_rules('directorypress_package_unlimited_'.$package->id, __('Listings unlimited', 'directorypress-frontend'), 'is_checked');
		}
		if ($validation->run()) {
			$result = get_option('directorypress_pmpro_packages') ? get_option('directorypress_pmpro_packages') : array();
			foreach ($directorypress_object->packages->packages_array as $package) {
				$result[$saveid][$package->id]['value'] = directorypress_get_input_value($validation->result_array(), 'directorypress_package_'.$package->id, 0);
				$result[$saveid][$package->id]['unlimited'] = directorypress_get_input_value($validation->result_array(), 'directorypress_package_unlimited_'.$package->id);
			}
			update_option('directorypress_pmpro_packages', $result);
		} else {
			$msg = -1;
			$msgt = sprintf(__("Error updating membership package: %s", 'directorypress-frontend'), $validation->error_string());
		}
	}

	public function user_profile_fields($user) {
var_dump($user);
		?>
		<table class="form-table">
			<tr>
				<th><label for="membership_package"><?php _e("Available directorytype listings", "directorypress-frontend"); ?></label></th>
				<td>
		<?php 
		if (!get_user_meta($user, 'directorypress_pmpro_package_listings')) {
			if (!pmpro_getMembershipLevelForUser())
				_e('Level');
		}
		?>
				</td>
			</tr>
		</table>
		<?php 
		var_dump(get_user_meta($user, 'directorypress_pmpro_package_listings'));
		var_dump(get_option('directorypress_pmpro_packages'));
	}
	
	public function pages_shortcode_packages() {
		$directorypress_pmpro_packages = get_option('directorypress_pmpro_packages') ? get_option('directorypress_pmpro_packages') : array();

		return directorypress_display_template(array(DPFL_TEMPLATES_PATH, 'pmpro/packages.tpl.php'), array('directorypress_pmpro_packages' => $directorypress_pmpro_packages), true);
	}
	
	public function scripts_styles() {
		global $post;
		if (!empty($post->post_content) && strpos($post->post_content, "[pmpro_packages]") !== false)
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
	}
	public function enqueue_scripts_styles() {
		global $directorypress_object, $directorypress_payments_instance, $directorypress_ratings_instance;
	
		$directorypress_object->enqueue_scripts_styles(true);
		$this->enqueue_scripts_styles(true);
		if ($directorypress_payments_instance)
			$directorypress_payments_instance->enqueue_scripts_styles(true);
		if ($directorypress_ratings_instance)
			$directorypress_ratings_instance->enqueue_scripts_styles(true);
	}
}

?>