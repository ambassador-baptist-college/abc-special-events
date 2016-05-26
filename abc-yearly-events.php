<?php
/*
 * Plugin Name: ABC Yearly Events
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-yearly-events/
 * Description: Yearly Events
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-yearly-events/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function yearly_events_post_type() {

    $labels = array(
        'name'                  => 'Yearly Events',
        'singular_name'         => 'Yearly Event',
        'menu_name'             => 'Yearly Events',
        'name_admin_bar'        => 'Yearly Event',
        'archives'              => 'Yearly Event Archives',
        'parent_item_colon'     => 'Parent Yearly Event:',
        'all_items'             => 'All Yearly Events',
        'add_new_item'          => 'Add New Yearly Event',
        'add_new'               => 'Add New',
        'new_item'              => 'New Yearly Event',
        'edit_item'             => 'Edit Yearly Event',
        'update_item'           => 'Update Yearly Event',
        'view_item'             => 'View Yearly Event',
        'search_items'          => 'Search Yearly Event',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into yearly event',
        'uploaded_to_this_item' => 'Uploaded to this yearly event',
        'items_list'            => 'Yearly Events list',
        'items_list_navigation' => 'Yearly Events list navigation',
        'filter_items_list'     => 'Filter yearly events list',
    );
    $rewrite = array(
        'slug'                  => 'news-events/event/',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Yearly Event',
        'description'           => 'Yearly Events',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-calendar-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'news-events/events/all',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'yearly_event', $args );

}
add_action( 'init', 'yearly_events_post_type', 0 );

// Modify the page title
function filter_yearly_event_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'yearly_event' ) ) {
          $title = 'Yearly Events';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_yearly_event_page_title' );
add_filter( 'get_the_archive_title', 'filter_yearly_event_page_title' );

// Sort archive
function sort_yearly_events_by_date( $query ) {
    if ( is_post_type_archive( 'yearly_event' ) && ! is_admin() ) {
        $query->set( 'orderby',     'meta_value_num' );
        $query->set( 'order',       'ASC' );
        $query->set( 'meta_key',    'begin_date' );
    }

    return $query;
}
add_filter( 'pre_get_posts', 'sort_yearly_events_by_date' );
