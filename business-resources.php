<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
