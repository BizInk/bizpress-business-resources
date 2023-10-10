<?php
/**
 * Plugin Name: BizPress Business Resources
 * Description: Show business resources on your site. Automatically updated by the Bizink team.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.2.5
 * Text Domain: bizink-client-business
 * Domain Path: /languages
 */

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Updater
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker('https://github.com/BizInk/bizpress-business-resources',__FILE__,'bizpress-business-resources');
// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
// Using a private repository, specify the access token 
$myUpdateChecker->setAuthentication('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');

add_action( 'plugins_loaded', 'bizpress_load_businessresources' );
function bizpress_load_businessresources() {
    if(is_plugin_active("bizpress-client/bizink-client.php")){
		require 'business-resources.php';
	}
}