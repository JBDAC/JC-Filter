<?php
/**
* Plugin Name: JC Filter
* Description: Filter posts and pages based on the jc_filter custom field. Display an empty page when nothing matches the jc_filter. Usage: [jc_filter include="includeA, includeB" exclude="excludeMe" date_format="Y-m-d" date_position="start" order="desc"]
 (This will create a list of posts drawn from posts which have a custom field called jc_filter with a relevant value. Use PHP date format characters.) So that posts are also hidden from the default 'uncategorised' default category, we add a filter for this when not in admin mode.
* Version: 1.0
* Author: JBDAC
**/

function display_posts_with_jc_filter($include_values, $exclude_values, $date_format, $date_position, $order) {
    global $post;

    // Splitting the values by commas to handle multiple values
    $include_values = !empty($include_values) ? explode(',', $include_values) : array();
    $exclude_values = !empty($exclude_values) ? explode(',', $exclude_values) : array();

    // Building the meta query
    $meta_query = array('relation' => 'AND');
    foreach ($include_values as $value) {
        $meta_query[] = array(
            'key' => 'jc_filter',
            'value' => trim($value),
            'compare' => '='
        );
    }
    foreach ($exclude_values as $value) {
        $meta_query[] = array(
            'key' => 'jc_filter',
            'value' => trim($value),
            'compare' => '!='
        );
    }

    // Query arguments
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'date',
	'order' => strtoupper($order), // Sort order (ASC or DESC)
        'meta_query' => $meta_query
    );

    // Fetching the posts
    $filtered_posts = get_posts($args);

//    echo '<pre>Debug: ';
//    print_r($filtered_posts);
//    echo '</pre>';

    if (!empty($filtered_posts)) {
        ob_start();
        echo '<ul>';
        foreach ($filtered_posts as $post) {
            setup_postdata($post);
            $formatted_date = get_the_date($date_format);
            echo '<li>';
            if ($date_position === 'start') {
                echo $formatted_date . ' - ';
            }
            echo '<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
            if ($date_position === 'end') {
                echo ' - ' . $formatted_date;
            }
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return 'No posts found with specified jc_filter values.';
    }
}

function jc_filter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'include' => '',
        'exclude' => '',
        'date_format' => 'F j, Y', // default format e.g., January 1, 2024
        'date_position' => 'end', // default position
	'order' => 'desc', // default order
    ), $atts, 'jc_filter_posts');

    return display_posts_with_jc_filter($atts['include'], $atts['exclude'], $atts['date_format'], $atts['date_position'], $atts['order']);
}

add_shortcode('jc_filter', 'jc_filter_shortcode');

//Hide the default (uncategorized) category:

// Function to exclude 'Uncategorized' from get_terms
function jc_filter_exclude_uncategorized_from_get_terms($terms, $taxonomies, $args){
    // Check if 'category' is part of the taxonomies
    if (!in_array('category', $taxonomies)) {
        return $terms;
    }

    $uncategorized_id = get_option('default_category'); // Get the ID of 'Uncategorized'

    // Filter out the 'Uncategorized' term
    return array_filter($terms, function($term) use ($uncategorized_id) {
        return $term->term_id != $uncategorized_id;
    });
}


// Apply the filters only if not in admin area
if (!is_admin()) {
    add_filter('get_terms', 'jc_filter_exclude_uncategorized_from_get_terms', 10, 3);
}
