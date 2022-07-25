<?php
/**
 * Plugin Name: BizPress Business Resources
 * Description: Show business resources on your site. Automatically updated by the Bizink team.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.0
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
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/BizInk/bizpress-business-resources',__FILE__,'bizpress-business-resources');
// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
// Using a private repository, specify the access token 
$myUpdateChecker->setAuthentication('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');

function business_settings_fields( $fields, $section ) {

	if ( 'bizink-client_basic' != $section['id'] ) return $fields;

	$fields['business_content_page'] = array(
		'id'      => 'business_content_page',
		'label'     => __( 'Bizink Client Business', 'bizink-client' ),
		'type'      => 'select',
		'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizink-content]</code> shortcode.', 'bizink-client' ),
		'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
		// 'chosen'	=> true,
		'required'	=> true,
	);

	return $fields;
}
add_filter( 'cx-settings-fields', 'business_settings_fields', 10, 2 );

function business_content( $types ) {
	$types[] = [
		'key' 	=> 'business_content_page',
		'type'	=> 'business-lifecycle'
	];

	return $types;
}
add_filter( 'bizink-content-types', 'business_content' );
