<?php
/**
 * Plugin Name: ABC Special Events
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 * Description: Special Events
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-special-events/
 *
 * @package abc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CPT.
 *
 * @return void
 */
function special_events_post_type() {
	$labels  = array(
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
		'slug'       => 'news/event',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => 'Special Event',
		'description'         => 'Special Events',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'excerpt' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-calendar-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'show_in_rest'        => true,
		'can_export'          => true,
		'has_archive'         => 'news/events/all',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'special_event', $args );

}
add_action( 'init', 'special_events_post_type', 0 );

/**
 * Modify the page title
 *
 * @param string $title Page title.
 * @param int    $id    Post ID.
 *
 * @return string       Page title.
 */
function filter_special_event_page_title( $title, $id = null ) {
	if ( is_post_type_archive( 'special_event' ) ) {
		$title = 'Special Events';
	}

	return $title;
}
add_filter( 'custom_title', 'filter_special_event_page_title' );
add_filter( 'get_the_archive_title', 'filter_special_event_page_title' );

/**
 * Sort archive.
 *
 * @param WP_Query $query Query object.
 *
 * @return WP_Query       Query object.
 */
function sort_special_events_by_date( $query ) {
	if ( is_post_type_archive( 'special_event' ) && $query->is_main_query() && ! is_admin() ) {
		$meta_query = $query->get( 'meta_query' );

		if ( is_array( $meta_query ) ) {
			$meta_query[] = array(
				'begin_date_clause' => array(
					'meta_key' => 'begin_date',
					'compare'  => 'EXISTS',
				),
			);

			$query->set(
				'orderby',
				array(
					'begin_date_clause ASC',
					'end_date ASC',
				)
			);
			$query->set( 'meta_key', 'begin_date' );
			$query->set( 'meta_value', date( 'Ymd' ) );
			$query->set( 'meta_compare', '>=' );
			$query->set( 'type', 'NUMERIC' );
			$query->set( 'meta_query', $meta_query );
		}
	}

	return $query;
}
add_filter( 'pre_get_posts', 'sort_special_events_by_date' );

/**
 * Add custom archive template.
 *
 * @param string $archive_template Template file.
 *
 * @return string                  Template file.
 */
function get_special_event_archive_template( $archive_template ) {
	global $post;
	if ( is_post_type_archive( 'special_event' ) ) {
		$archive_template = dirname( __FILE__ ) . '/archive-special_event.php';
	}
	return $archive_template;
}
add_filter( 'archive_template', 'get_special_event_archive_template' );

/**
 * Add custom single template.
 *
 * @param string $single_template Path to template file.
 *
 * @return string                 Path to template file.
 */
function get_special_event_single_template( $single_template ) {
	global $post;

	if ( 'special_event' === $post->post_type ) {
		$single_template = dirname( __FILE__ ) . '/single-special_event.php';
	}
	return $single_template;
}
add_filter( 'single_template', 'get_special_event_single_template' );

/**
 * Add custom entry meta.
 *
 * @return void
 */
function print_special_event_meta_info() {
	// Date.
	if ( get_field( 'begin_date' ) ) {
		printf(
			'<h3>%1$s</h3>
		<p class="event-dates">%2$s</p>',
			get_field( 'end_date' ) ? 'Dates' : 'Date',
			get_special_event_date_format()
		);
	}

	// Location.
	if ( get_field( 'location' ) ) {
		$location = get_field( 'location' );

		printf(
			'<h3>Location</h3>
		<p class="location"><a href="https://www.google.com/maps/search/%1$s" target="_blank">%1$s</a></p>',
			$location['address']
		);
	}

	// Keynote speakers.
	if ( get_field( 'keynote_speaker' ) ) {

		$speaker_args = array(
			'post_type'              => 'special_speaker',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'post__in'               => get_field( 'keynote_speaker' ),
			'order'                  => 'ASC',
			'orderby'                => 'meta_value',
			'meta_key'               => 'sort_order',
			'cache_results'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		$special_speaker_query = new WP_Query( $speaker_args );

		if ( $special_speaker_query->have_posts() ) {
			echo '<h3>Keynote Speaker' . ( $special_speaker_query->post_count === 1 ? '' : 's' ) . '</h3>';

			while ( $special_speaker_query->have_posts() ) {
				$special_speaker_query->the_post();
				echo wp_kses_post( get_featured_speaker_info( get_the_ID(), $special_speaker_query->found_posts ) );
			}
		}

		wp_reset_postdata();
	}

	// Speakers.
	if ( get_field( 'special_speaker' ) ) {

		$speaker_args = array(
			'post_type'              => 'special_speaker',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'post__in'               => get_field( 'special_speaker' ),
			'order'                  => 'ASC',
			'orderby'                => 'meta_value',
			'meta_key'               => 'sort_order',
			'cache_results'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		$special_speaker_query = new WP_Query( $speaker_args );

		if ( $special_speaker_query->have_posts() ) {
			echo '<h3>Special Speaker' . ( 1 === $special_speaker_query->post_count ? '' : 's' ) . '</h3>';

			while ( $special_speaker_query->have_posts() ) {
				$special_speaker_query->the_post();
				echo wp_kses_post( get_featured_speaker_info( get_the_ID(), $special_speaker_query->found_posts ) );
			}
		}

		wp_reset_postdata();
	}
}
add_action( 'special_event_entry_meta', 'print_special_event_meta_info' );

// Helper function to format dates
function get_special_event_date_format() {
	// date
	$begin_date           = DateTime::createFromFormat( 'Ymd', get_field( 'begin_date' ) );
	$begin_date_formatted = $begin_date->format( 'F j' );

	// time
	$begin_time = get_field( 'begin_time' );
	$end_time   = get_field( 'end_time' );

	// time for microdata
	if ( $begin_time ) {
		if ( strpos( $begin_time, ' AM' ) !== false ) {
			$begin_time_microdata = explode( ':', str_replace( ' AM', '', $begin_time ) );
			$begin_hour           = $begin_time_microdata[0];
			$begin_minute         = $begin_time_microdata[1];
		} elseif ( strpos( $begin_time, ' PM' ) !== false ) {
			$begin_time_microdata = explode( ':', str_replace( ' PM', '', $begin_time ) );
			$begin_hour           = $begin_time_microdata[0] + 12;
			$begin_minute         = $begin_time_microdata[1];
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
		$end_date_microdata = $end_date->format( 'Y-m-dT' ) . ( empty( $end_time ) ? '12:00' : $end_hour . ':' . $end_minute );
	} else {
		$begin_date_formatted .= ', ' . $begin_date->format( 'Y' );
		$end_date_formatted    = null;

		if ( $begin_time ) {
			$begin_date_formatted .= ', ' . $begin_time;
		}
		if ( $end_time ) {
			$begin_date_formatted .= '&ndash;' . $end_time;
		}

		$end_date_microdata = $begin_date->format( 'Y-m-dT' ) . ( empty( $begin_time ) ? '12:00' : $begin_hour . ':' . $begin_minute );
	}

	$begin_date_microdata = $begin_date->format( 'Y-m-dT' ) . ( get_field( 'begin_time' ) ? $begin_hour . ':' . $begin_minute : '12:00' );

	$location = get_field( 'location' );

	// Speakers.
	$special_speakers = (array) get_field( 'special_speaker' );
	$keynote_speakers = (array) get_field( 'keynote_speaker' );

	$speaker_ids   = array_merge( $special_speakers, $keynote_speakers );
	$speaker_names = array();

	foreach ( $speaker_ids as $speaker ) {
		if ( ! empty( $speaker ) ) {
			$speaker_names[] = get_the_title( $speaker );
		}
	}

	// microdata
	$microdata = sprintf(
		'<script type="application/ld+json">
			{
				"@context": "http://www.schema.org",
				"@type": "Event",
				"name": "%s",
				"url": "%s",
				"description": "%s",
				"startDate": "%s",
				"endDate": "%s",
				"location": {
					"@type": "Place",
					"name": "%s",
					"address": "%s"
				},
				"image": "%s",
				"performer": "%s"
			}
		</script>',
		get_the_title(),
		get_permalink(),
		str_replace( '"', '\'', wp_strip_all_tags( get_the_excerpt() ) ),
		$begin_date_microdata,
		$end_date_microdata,
		strtok( $location['address'], ',' ),
		$location['address'],
		get_the_post_thumbnail_url(),
		implode( ', ', $speaker_names )
	);

	return $begin_date_formatted . $end_date_formatted . $microdata;
}

// Shortcode for speakers
function abc_special_speakers_shortcode( $atts ) {
	global $post;

	$atts             = shortcode_atts(
		array(
			'align'    => 'left',
			'show_bio' => false,
		),
		$atts,
		'special_speakers'
	);
	$shortcode_output = null;

	return abc_speakers_for_shortcode( get_field( 'special_speaker' ), $atts );
}
add_shortcode( 'special_speaker', 'abc_special_speakers_shortcode' );
add_shortcode( 'special_speakers', 'abc_special_speakers_shortcode' );

// Shortcode for speakers
function abc_keynote_speakers_shortcode( $atts ) {
	global $post;

	$atts             = shortcode_atts(
		array(
			'align'    => 'left',
			'show_bio' => false,
		),
		$atts,
		'keynote_speakers'
	);
	$shortcode_output = null;

	return abc_speakers_for_shortcode( get_field( 'keynote_speaker' ), $atts );
}
add_shortcode( 'keynote_speaker', 'abc_keynote_speakers_shortcode' );
add_shortcode( 'keynote_speakers', 'abc_keynote_speakers_shortcode' );

// Helper function for shortcodes
function abc_speakers_for_shortcode( $speakers_array, $atts ) {
	 $output = null;

	// Bail if there are no speakers to show.
	if ( empty( $speakers_array ) ) {
		return '';
	}

	$speaker_args = array(
		'post_type'              => 'special_speaker',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'post__in'               => $speakers_array,
		'order'                  => 'ASC',
		'orderby'                => 'meta_value',
		'meta_key'               => 'sort_order',
		'cache_results'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	$special_speaker_query = new WP_Query( $speaker_args );

	if ( $special_speaker_query->have_posts() ) {
		while ( $special_speaker_query->have_posts() ) {
			$special_speaker_query->the_post();
			$output .= '<figure class="wp-caption align' . $atts['align'] . '">
				' . get_the_post_thumbnail( get_the_ID(), 'faculty', array( 'class' => 'align' . $atts['align'] ) ) . '
				<figcaption class="wp-caption-text">' . get_the_title();
			if ( $atts['show_bio'] ) {
				$output .= '<br/>' . get_the_content();
			}
			$output .= '</figcaption></figure>';
		}
	}

	wp_reset_postdata();

	return $output;
}

/**
 * Get featured speaker content
 *
 * @param  integer $id         special event post ID
 * @param  integer $post_count number of speakers
 * @return string  HTML content
 */
function get_featured_speaker_info( $id, $post_count ) {
	$size = '';

	if ( $post_count >= 6 ) {
		$size = 'half-width';
	}

	ob_start();

	echo '<figure class="event-speaker ' . $size . '">';
	if ( has_post_thumbnail( $id ) ) {
		echo '<a href="' . get_permalink( $id ) . '" target="_blank">' . get_the_post_thumbnail( $id, 'special-event-sidebar-s' ) . '</a>';
	}
	echo '<figcaption><a href="' . get_permalink( $id ) . '" target="_blank">' . get_the_title( $id ) . '</a><br/>
	<span class="wp-caption-text">' . get_the_excerpt( $id ) . '</span></figcaption>
	';

	if ( get_the_excerpt( $id ) !== get_the_content( $id ) ) {
		echo '<a class="read-more" href="' . esc_url( get_permalink( $id ) ) . '">Read more&rarr;</a>';
	}

	echo '</figure>';

	return ob_get_clean();
}
