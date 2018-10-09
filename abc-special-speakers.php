<?php
/*
 * Plugin Name: ABC Special Speakers
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 * Description: Special Speakers
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function special_speakers_post_type() {

    $labels = array(
        'name'                  => 'Special Speakers',
        'singular_name'         => 'Special Speaker',
        'menu_name'             => 'Special Speakers',
        'name_admin_bar'        => 'Special Speaker',
        'archives'              => 'Special Speaker Archives',
        'parent_item_colon'     => 'Parent Special Speaker:',
        'all_items'             => 'All Special Speakers',
        'add_new_item'          => 'Add New Special Speaker',
        'add_new'               => 'Add New',
        'new_item'              => 'New Special Speaker',
        'edit_item'             => 'Edit Special Speaker',
        'update_item'           => 'Update Special Speaker',
        'view_item'             => 'View Special Speaker',
        'search_items'          => 'Search Special Speaker',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into special speaker',
        'uploaded_to_this_item' => 'Uploaded to this special speaker',
        'items_list'            => 'Special Speakers list',
        'items_list_navigation' => 'Special Speakers list navigation',
        'filter_items_list'     => 'Filter special speakers list',
    );
    $rewrite = array(
        'slug'                  => 'speaker',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Special Speaker',
        'description'           => 'Special Speakers',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'speakers/all',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'special_speaker', $args );

}
add_action( 'init', 'special_speakers_post_type', 0 );

// Modify the page title
function filter_special_speaker_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'special_speaker' ) ) {
          $title = 'Special Speakers';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_special_speaker_page_title' );
add_filter( 'get_the_archive_title', 'filter_special_speaker_page_title' );

// Sort archive
function sort_special_speakers_by_date( $query ) {
    if ( ( is_post_type_archive( 'special_speaker' ) || 'special_speaker' == $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) && ! is_admin() ) {
        $query->set( 'orderby',     'meta_value' );
        $query->set( 'order',       'ASC' );
        $query->set( 'meta_key',    'sort_order' );
    }

    return $query;
}
add_filter( 'pre_get_posts', 'sort_special_speakers_by_date' );

// Add backend JS
function add_speaker_backend_js() {
    wp_enqueue_script( 'speaker-backend', plugins_url( 'js/speaker-backend.min.js', __FILE__ ), array( 'jquery' ) );
}
add_action( 'admin_enqueue_scripts', 'add_speaker_backend_js' );

// add custom image sizes
add_action( 'after_setup_theme', 'abc_custom_image_sizes_speakers' );
function abc_custom_image_sizes_speakers() {
    add_image_size( 'special-event-sidebar-s', 180, 240, true );
    add_image_size( 'special-event-sidebar-m', 270, 360, true );
    add_image_size( 'special-event-sidebar-l', 360, 480, true );
}
