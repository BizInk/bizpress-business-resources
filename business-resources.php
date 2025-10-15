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

	if('bizpress_seo' == $section['id']){
		$fields['business'] = array(
            'id' => 'business',
            'label'	=> __( 'Business Resources', 'bizink-client' ),
            'type' => 'divider'
        );
		$fields['business_sitemap'] = array(
            'id' => 'business_sitemap',
            'label'	=> __( 'Enable Sitemap - Business Resources', 'bizink-client' ),
            'type' => 'switch',
			'default' => 'on',
			'desc' => __( 'Enable the sitemap for the business resources page.', 'bizink-client' ),
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
		add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=business-resources&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename=business-resources&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename=business-resources&topic=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename=business-resources&type=$matches[1]','top');

        add_rewrite_tag('%business_resources.xml%', '([^&]+)', 'bizpressxml=');
		add_rewrite_rule('^(business_resources\.xml)?$','index.php?bizpressxml=business-resources','top');

		if(get_option('bizpress_xero_flush_update',0) < 1){
			flush_rewrite_rules();
			update_option('bizpress_xero_flush_update',1);
		}
    }
}

add_action('parse_request','bizpress_xbusiness_resourcesxml_request', 10, 1);
function bizpress_xbusiness_resourcesxml_request($wp){
	$ending = substr(get_option('permalink_structure'), -1) == '/' ? '/':'';
	$ending = substr(get_option('permalink_structure'), -1) == '/' ? '/':'';
	if ( array_key_exists( 'bizpressxml', $wp->query_vars ) && $wp->query_vars['bizpressxml'] == 'business-resources' ){
		$post = bizpress_get_businesscontent_page_object();
		if( is_object( $post ) && get_post_type( $post ) == "page" ){
			$data = get_transient("bizinktype_".md5('business-content'));
			if(empty($data)){
				$data = bizink_get_content('business-content', 'topics');
				set_transient( "bizinktype_".md5('business-content'), $data, (DAY_IN_SECONDS * 2) );
			}
			header('Content-Type: text/xml; charset=UTF-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<?xml-stylesheet type="text/xsl" href="'. plugins_url('wordpress-seo/css/main-sitemap.xsl', dirname(__FILE__)) .'"?>';
			echo '<?xml-stylesheet type="text/xsl" href="'. plugins_url('wordpress-seo/css/main-sitemap.xsl', dirname(__FILE__)) .'"?>';
			echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			echo '<url>';
				echo '<loc>'.esc_url(get_home_url().'/'.$post->post_name.$ending).'</loc>';
				echo '<loc>'.esc_url(get_home_url().'/'.$post->post_name.$ending).'</loc>';
			echo '</url>';
			
			if(empty($data->posts) == false){
				foreach($data->posts as $item){
					echo '<url>';
					echo '<loc>'.esc_url(get_home_url().'/'.$post->post_name.'/'.$item->slug.$ending).'</loc>';
					echo '<loc>'.esc_url(get_home_url().'/'.$post->post_name.'/'.$item->slug.$ending).'</loc>';
					if($item->thumbnail){
						echo '<image:image>';
						echo '<image:loc>'. $item->thumbnail .'</image:loc>';
						echo '</image:image>'; 
					}
					echo '</url>';
				}
			}
			echo '</urlset>';
		}
		die();
	}
}

add_filter('query_vars', 'bizpress_businesscontent_qurey');
function bizpress_businesscontent_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}

add_filter('query_vars', 'bizpress_business_resourcesxml_query');
function bizpress_business_resourcesxml_query($vars) {
    $vars[] = "bizpressxml";
    return $vars;
}

function bizpress_business_resources_sitemap_custom_items( $sitemap_custom_items ) {
	$enable_sitemap = cxbc_get_option( 'bizpress_seo', 'business_sitemap' );
	if ( $enable_sitemap == 'off' || $enable_sitemap == 0 || $enable_sitemap == false ) {
		return $sitemap_custom_items;
	}

    $sitemap_custom_items .= '
	<sitemap>
		<loc>'.get_home_url().'/business_resources.xml</loc>
	</sitemap>';
    return $sitemap_custom_items;
}

add_filter( 'wpseo_sitemap_index', 'bizpress_business_resources_sitemap_custom_items' );

function bizpress_business_resources_content_manager_fields($fields){
	$data = null;
	if(function_exists('bizink_get_content')){
		$data = bizink_get_content( 'business-content', 'topics' );
	}
	$fields['business_resources'] = array(
		'id' => 'business_resources',
		'label'	=> __( 'Business Resources', 'bizink-client' ),
		'posts' => $data ? $data->posts : array(),
	);
	return $fields;
}
add_filter('bizpress_content_manager_fields','bizpress_business_resources_content_manager_fields');