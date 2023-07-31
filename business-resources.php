<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function business_settings_fields( $fields, $section ) {
    $pageselect = false;
	if(defined('CXBPC')){
		$bizpress = get_plugin_data( CXBPC );
		$v = intval(str_replace('.','',$bizpress['Version']));
		if($v >= 151){
			$pageselect = true;
		}
	}
    if('bizink-client_basic' == $section['id']){
        $fields['business_content_page'] = array(
            'id'      => 'business_content_page',
            'label'     => __( 'Business Lifecycle', 'bizink-client' ),
            'type'      =>  $pageselect ? 'pageselect':'select',
            'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-client' ),
            'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
            'required'	=> false,
            'default_page' => [
				'post_title' => 'Business Lifecycle',
				'post_content' => '[bizpress-content]',
				'post_status' => 'publish',
				'post_type' => 'page'
			]
        );
    }

    if('bizink-client_content' == $section['id']){
        $fields['business'] = array(
            'id' => 'business',
            'label'	=> __( 'Business Lifecycle', 'bizink-client' ),
            'type' => 'divider'
        );
        $fields['business_title'] = array(
            'id' => 'business_title',
            'label'     => __( 'Business Title', 'bizink-client' ),
            'type'      => 'text',
            'default'   => __( 'Business Lifecycle', 'bizink-client' ),
            'required'	=> true,
        );
        $fields['business_desc'] = array(
            'id'      	=> 'business_desc',
            'label'     => __( 'Business Lifecycle Description', 'bizink-client' ),
            'type'      => 'textarea',
            'default'   => __( 'Free resources to help you with the Business Lifecycle.', 'bizink-client' ),
            'required'	=> false,
        );
    }

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
