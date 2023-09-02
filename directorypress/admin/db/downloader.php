<?php 

include_once DIRECTORYPRESS_PATH. 'admin/directorypress-update-checker/directorypress-update-checker.php';
$protocol		= is_ssl() ? 'https://' : 'http://';
$url = $protocol .'assets.designinvento.net/plugins/directorypress/3-2/update/';

if (file_exists(DIRECTORYPRESS_PATH .'directorypress.php')) {
	$directorypress = Puc_v4_Factory::buildUpdateChecker(
		$url .'directorypress.json',
		DIRECTORYPRESS_PATH .'directorypress.php',
		'directorypress'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-extended-locations/directorypress-extended-locations.php')) {
	$dpel = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-extended-locations.json',
		WP_PLUGIN_DIR .'/directorypress-extended-locations/directorypress-extended-locations.php',
		'directorypress-extended-locations'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-frontend/directorypress-frontend.php')) {
	$dpfl = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-frontend-listing.json',
		WP_PLUGIN_DIR .'/directorypress-frontend/directorypress-frontend.php',
		'directorypress-frontend'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-maps/directorypress-maps.php')) {
	$dpm = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-maps.json',
		WP_PLUGIN_DIR .'/directorypress-maps/directorypress-maps.php',
		'directorypress-maps'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-multidirectory/directorypress-multidirectory.php')) {
	$dpmd = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-multi-directory.json',
		WP_PLUGIN_DIR .'/directorypress-multidirectory/directorypress-multidirectory.php',
		'directorypress-multidirectory'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-payment-manager/directorypress-payment-manager.php')) {
	$dppm = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-payment-manager.json',
		WP_PLUGIN_DIR .'/directorypress-payment-manager/directorypress-payment-manager.php',
		'directorypress-payment-manager'
	);
}
if (file_exists(WP_PLUGIN_DIR .'/directorypress-frontend-messages/directorypress-frontend-messages.php')) {
	$dpfm = Puc_v4_Factory::buildUpdateChecker(
		$url .'dp-frontend-messages.json',
		WP_PLUGIN_DIR .'/directorypress-frontend-messages/directorypress-frontend-messages.php',
		'directorypress-frontend-messages'
	);
}
