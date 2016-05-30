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
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', ),
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
function print_special_event_meta_info() {
    // date
    if ( get_field( 'begin_date' ) ) {
        printf( '<h3>%1$s</h3>
        <p class="event-dates">%2$s</p>',
                get_field( 'end_date' ) ? 'Dates' : 'Date',
                get_special_event_date_format( $post )
        );
    }

    // location
    if ( get_field( 'location' ) ) {
        $location = get_field( 'location' );

        printf( '<h3>Location</h3>
        <p class="location"><a href="https://www.google.com/maps/search/%1$s" target="_blank">%1$s</a></p>',
               $location['address']
        );
    }

    // keynote speakers
    if ( get_field( 'keynote_speaker' ) ) {
        echo '<h3>Keynote Speakers</h3>
        <ul class="event-speakers">';

        $speaker_args = array(
            'post_type'         => 'special_speaker',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'post__in'          => get_field( 'keynote_speaker' ),
            'order'             => 'ASC',
            'orderby'           => 'meta_value',
            'meta_key'          => 'sort_order',
        );

        $special_speaker_query = new WP_Query( $speaker_args );

        if ( $special_speaker_query->have_posts() ) {
            while ( $special_speaker_query->have_posts() ) {
                $special_speaker_query->the_post();
                echo '<li title="' . get_the_excerpt() . '">' . get_the_title() . '</li>';
            }
        }

        wp_reset_query();

        echo '</ul>';
    }

    // speakers
    if ( get_field( 'special_speaker' ) ) {
        echo '<h3>Special Speakers</h3>
        <ul class="event-speakers">';

        $speaker_args = array(
            'post_type'         => 'special_speaker',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'post__in'          => get_field( 'special_speaker' ),
            'order'             => 'ASC',
            'orderby'           => 'meta_value',
            'meta_key'          => 'sort_order',
        );

        $special_speaker_query = new WP_Query( $speaker_args );

        if ( $special_speaker_query->have_posts() ) {
            while ( $special_speaker_query->have_posts() ) {
                $special_speaker_query->the_post();
                echo '<li title="' . get_the_excerpt() . '">' . get_the_title() . '</li>';
            }
        }

        wp_reset_query();

        echo '</ul>';
    }
}
add_action( 'special_event_entry_meta', 'print_special_event_meta_info' );

// Helper function to format dates
function get_special_event_date_format( $post ) {
    // date
    $begin_date = DateTime::createFromFormat( 'Ymd', get_field( 'begin_date' ) );
    $begin_date_formatted = $begin_date->format( 'F j' );

    // time
    $begin_time = get_field( 'begin_time' );
    $end_time = get_field( 'end_time' );

    // time for microdata
    if ( $begin_time ) {
        if ( strpos( $begin_time, ' AM') !== false ) {
            $begin_time_microdata = explode( ':', str_replace( ' AM', '', $begin_time ) );
            $begin_hour = $begin_time_microdata[0];
            $begin_minute = $begin_time_microdata[1];
        } elseif ( strpos( $begin_time, ' PM' ) !== false ) {
            $begin_time_microdata = explode( ':', str_replace( ' PM', '', $begin_time ) );
            $begin_hour = $begin_time_microdata[0] + 12;
            $begin_minute = $begin_time_microdata[1];
        }
    }

    // format
    if ( get_field( 'end_date' ) ) {
        $end_date = DateTime::createFromFormat( 'Ymd', get_field( 'end_date' ) );

        $end_date_formatted = $end_date->format( 'j, Y' );
        if ( $begin_date->format( 'm' ) != $end_date->format( 'm' ) ) {
            $end_date_formatted = $end_date->format( 'F ' ) . $end_date_formatted;
        }
        $end_date_formatted = '&ndash;' . $end_date_formatted;
    } else {
        $begin_date_formatted .= ', ' . $begin_date->format( 'Y' );
        $end_date_formatted = NULL;

        if ( $begin_time ) {
            $begin_date_formatted .= ', ' . $begin_time;
        }
        if ( $end_time ) {
            $begin_date_formatted .= '&ndash;' . $end_time;
        }
    }

    // microdata
    $microdata = '<script type="application/ld+json">
        {
          "@context": "http://www.schema.org",
          "@type": "Event",
          "name": "' . get_the_title() . '",
          "url": "' . get_permalink() . '",
          "description": "' . get_the_excerpt() . '",
          "startDate": "' . $begin_date->format( 'Y-m-dT' ) . ( get_field( 'begin_time' ) ? $begin_hour . ':' . $begin_minute : '12:00' ) . '",
          "location": {
            "@type": "Place",
            "name": "' . strtok( get_field( 'location' )['address'], ',' ) . '"
          }
        }
         </script>';

    return $begin_date_formatted . $end_date_formatted . $microdata;
}
