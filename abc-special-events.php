<?php
/*
 * Plugin Name: ABC Special Events
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 * Description: Special Events
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function special_events_post_type() {

    $labels = array(
        'name'                  => 'Special Events',
        'singular_name'         => 'Special Event',
        'menu_name'             => 'Special Events',
        'name_admin_bar'        => 'Special Event',
        'archives'              => 'Special Event Archives',
        'parent_item_colon'     => 'Parent Special Event:',
        'all_items'             => 'All Special Events',
        'add_new_item'          => 'Add New Special Event',
        'add_new'               => 'Add New',
        'new_item'              => 'New Special Event',
        'edit_item'             => 'Edit Special Event',
        'update_item'           => 'Update Special Event',
        'view_item'             => 'View Special Event',
        'search_items'          => 'Search Special Event',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into special event',
        'uploaded_to_this_item' => 'Uploaded to this special event',
        'items_list'            => 'Special Events list',
        'items_list_navigation' => 'Special Events list navigation',
        'filter_items_list'     => 'Filter special events list',
    );
    $rewrite = array(
        'slug'                  => 'news-events/event',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Special Event',
        'description'           => 'Special Events',
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
    register_post_type( 'special_event', $args );

}
add_action( 'init', 'special_events_post_type', 0 );

// Modify the page title
function filter_special_event_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'special_event' ) ) {
          $title = 'Special Events';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_special_event_page_title' );
add_filter( 'get_the_archive_title', 'filter_special_event_page_title' );

// Sort archive
function sort_special_events_by_date( $query ) {
    if ( is_post_type_archive( 'special_event' ) && ! is_admin() ) {
        $query->set( 'orderby',     'meta_value_num' );
        $query->set( 'order',       'ASC' );
        $query->set( 'meta_key',    'begin_date' );
    }

    return $query;
}
add_filter( 'pre_get_posts', 'sort_special_events_by_date' );

// Add custom archive template
function get_special_event_archive_template( $archive_template ) {
    global $post;
    if ( is_post_type_archive ( 'special_event' ) ) {
        $archive_template = dirname( __FILE__ ) . '/archive-special_event.php';
    }
    return $archive_template;
}
add_filter( 'archive_template', 'get_special_event_archive_template' ) ;

// Add custom single template
function get_special_event_single_template( $single_template ) {
    global $post;

    if ( 'special_event' == $post->post_type ) {
        $single_template = dirname( __FILE__ ) . '/single-special_event.php';
    }
    return $single_template;
}
add_filter( 'single_template', 'get_special_event_single_template' );

// Add custom entry meta
function special_event_entry_meta() {
    printf( '<span class="event-dates">%1$s</span>',
            get_special_event_date_format( $post )
    );
}

// Helper function to format dates
function get_special_event_date_format( $post ) {
    // date
    $begin_date = DateTime::createFromFormat( 'Ymd', get_field( 'begin_date' ) );
    $begin_date_formatted = $begin_date->format( 'F j' );

    if ( get_field( 'end_date' ) ) {
        $end_date = DateTime::createFromFormat( 'Ymd', get_field( 'end_date' ) );

        if ( $begin_date->format( 'Y' ) != $end_date->format( 'Y' ) ) {
            $begin_date_formatted .= $begin_date->format( ', Y' );
        }
        $end_date_formatted = $end_date->format( 'j, Y' );
        if ( $begin_date->format( 'm' ) != $end_date->format( 'm' ) ) {
            $end_date_formatted = $end_date->format( 'F ' ) . $end_date_formatted;
        }
        $end_date_formatted = '&ndash;' . $end_date_formatted;
    } else {
        $end_date_formatted = NULL;
    }

    return $begin_date_formatted . $end_date_formatted;
}
