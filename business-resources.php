<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'display_post_states', 'bizpress_businesscontent_post_states', 10, 2 );
function bizpress_businesscontent_post_states( $post_states, $post ) {
	$businesscontentResourcesPageID = intval(cxbc_get_option( 'bizink-client_basic', 'business_content_page' ));
    if ( $businesscontentResourcesPageID == $post->ID ) {
        $post_states['bizpress_businesscontent'] = __('BizPress Business Resources','bizink-client');
    }
    return $post_states;
}

function bizpress_businesscontent_settings_fields( $fields, $section ) {
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
            'label'     => __( 'Business Resources', 'bizink-client' ),
            'type'      =>  $pageselect ? 'pageselect':'select',
            'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-client' ),
            'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
            'required'	=> false,
            'default_page' => [
				'post_title' => 'Business Resources',
				'post_content' => '[bizpress-content]',
				'post_status' => 'publish',
				'post_type' => 'page'
			]
        );
    }

    if('bizink-client_content' == $section['id']){
        $fields['business'] = array(
            'id' => 'business',
            'label'	=> __( 'Business Resources', 'bizink-client' ),
            'type' => 'divider'
        );
        $fields['business_title'] = array(
            'id' => 'business_title',
            'label'     => __( 'Business Title', 'bizink-client' ),
            'type'      => 'text',
            'default'   => __( 'Business Resources', 'bizink-client' ),
            'required'	=> true,
        );
        $fields['business_desc'] = array(
            'id'      	=> 'business_desc',
            'label'     => __( 'Business Resources Description', 'bizink-client' ),
            'type'      => 'textarea',
            'default'   => __( 'Free resources to help you with your business.', 'bizink-client' ),
            'required'	=> false,
        );
    }

    return $fields;
}
add_filter( 'cx-settings-fields', 'bizpress_businesscontent_settings_fields', 10, 2 );

function business_content( $types ) {
    $types[] = [
        'key' 	=> 'business_content_page',
        'type'	=> 'business-content'
    ];
    return $types;
}
add_filter( 'bizink-content-types', 'business_content' );

if( !function_exists( 'bizpress_get_businesscontent_page_object' ) ){
	function bizpress_get_businesscontent_page_object(){
		$post_id = cxbc_get_option( 'bizink-client_basic', 'business_content_page' );
		$post = get_post( $post_id );
		return $post;
	}
}

add_action( 'init', 'bizpress_businesscontent_init');
function bizpress_businesscontent_init(){
    $post = bizpress_get_businesscontent_page_object();
    if( is_object( $post ) && get_post_type( $post ) == "page" ){
        add_rewrite_tag('%'.$post->post_name.'%', '([^&]+)', 'bizpress=');
		add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=business-content&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename=business-content&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename=business-content&topic=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename=business-content&type=$matches[1]','top');
    }
}

add_filter('query_vars', 'bizpress_businesscontent_qurey');
function bizpress_businesscontent_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}