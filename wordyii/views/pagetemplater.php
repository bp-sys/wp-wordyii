<?php
global $wordyii_templates;
/**
 * Initializes the plugin by setting filters and administration functions.
 */
function wordyii_load_templates() {
    // Add a filter to the attributes metabox to inject template into the cache.
    add_filter( 'page_attributes_dropdown_pages_args', 'wordyii_register_project_templates' );
    // Add a filter to the save post to inject out template into the page cache
    add_filter( 'wp_insert_post_data', 'wordyii_register_project_templates' );
    // Add a filter to the template include to determine if the page has our 
    // template assigned and return it's path
    add_filter( 'template_include', 'wordyii_view_project_template' );
}
/**
 * Adds our template to the pages cache in order to trick WordPress
 * into thinking the template file exists where it doens't really exist.
 */
function wordyii_register_project_templates( $atts ) {
    global $wordyii_templates;
    // Create the key used for the themes cache
    $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
    // Retrieve the cache list. 
    // If it doesn't exist, or it's empty prepare an array
    $t = wp_get_theme()->get_page_templates();
    if ( empty( $t ) ) {
        $t = array();
    } 
    // New cache, therefore remove the old one
    wp_cache_delete( $cache_key , 'themes');
    // Now add our template to the list of templates by merging our templates
    // with the existing templates array from the cache.
    $t = array_merge( $t, $wordyii_templates );
    // Add the modified cache to allow WordPress to pick it up for listing
    // available templates
    wp_cache_add( $cache_key, $t, 'themes', 1800 );
    return $atts;
} 
/**
 * Checks if the template is assigned to the page
 */
function wordyii_view_project_template( $template ) {
    global $wordyii_templates;
    global $post;
    if (!isset($wordyii_templates[get_post_meta( $post->ID, '_wp_page_template', true )] )) {
        return $template;
    } 
    $file = plugin_dir_path(__FILE__). get_post_meta( $post->ID, '_wp_page_template', true );
    // Just to be safe, we check if the file exist first
    if( file_exists( $file ) ) {
        return $file;
    } else {
        echo $file;
    }
    return $template;
}
// Custom templates array
$wordyii_templates = array(
    'template-example.php'     => 'Example Template',
);
// Action that adds custom templates to page attributes
add_action( 'plugins_loaded', 'wordyii_load_templates' );