<?php

/*
Plugin Name: Team Rocket Core
Plugin URI: http://uttyleracm.com
Description: Common function library for Team Rocket.
Version: 0.0.5
*/

/* Change log:
Vs 0.0.1	11/10/2016	Initial build.
Vs 0.0.2	11/16/2016	Added function to get category id by slug.
Vs 0.0.3	11/18/2016	Removed redundant trc_create_wp_post function.
Vs 0.0.4	11/18/2016	Created Team Rocket Core Tests function.
Vs 0.0.5	11/18/2016	Split getting posts and displaying posts functions.
*/

function trc_version() {
	// Return current plugin version number.
	return '0.0.5';
}

function trc_display_posts($posts) {
	// Displays all of the WordPress posts from the provided array.

	// Display the posts.
	foreach( $posts as $post ) {
		$description = $post['post_excerpt'];
		$link = get_permalink($post['ID']);
		$title = $post['post_title'];

		echo "
			<h1><a href=\"$link\">$title</a></h1>
			<p><em>$description</em></p>
		";
	}
}

function trc_is_non_empty_string($value) {
	// Checks that the value is a non-empty string.
	return is_string($value) && strlen($value) > 0;
}

function trc_is_real_category($category_slug) {
	// Checks that the provided string is a category that exists.

	// If it is a real category, this won't equal false.
	// 	If real category, returns true.
	//	If non-existent category, returns false.
	return get_category_by_slug($category_slug) != false;
}

function trc_is_valid_category_slug($category_slug) {
	// Checks that the provided slug is a valid category slug.

	// Check that provided slug is a non-empty string and real category.
	if( trc_is_non_empty_string($category_slug) ) {
		// It is a non-empty string.
		if( trc_is_real_category($category_slug) ) {
			// It is a real category.
			$slug_is_valid = true;
		}
		else {
			// Not a category that exists.
			$slug_is_valid = false;
		}
	}
	else {
		// Not a string or is an empty string.
		$slug_is_valid = false;
	}

	// Return whether the category slug is valid: true or false.
	return $slug_is_valid;
}

function trc_is_non_zero_integer($value) {
	// Checks that the provided value is an integer greater than or equal to 1.

	// Check that the value is an integer.
	$is_integer = is_int($value);

	// Check that the integer is greater than 0, i.e. 1 or more.
	$is_greater_or_equal_to_one = $is_integer && $value > 0;

	// Return whether it is a valid integer greater than 0.
	return $is_greater_or_equal_to_one;
}

function trc_get_category_id_by_slug($category_slug) {
	// Gets the integer category ID from the textual slug.

	if( trc_is_valid_category_slug($category_slug) ) {
		// Is a category that exists.

		// Get the category object.
		$category = get_category_by_slug($category_slug);

		// Get the numeric ID.
		$category_id = $category->term_id;

		// Return the ID.
		return $category_id;
	}
	else {
		// Not a valid string or not a category that exists.
		return false;
	}
}

function trc_get_recent_posts_by_category_slug($category_slug, $number_of_posts = 3) {
	// Gets the recent posts by textual category slug.

	// Check that the provided slug is valid and a real category.
	$is_valid_category_slug = trc_is_valid_category_slug($category_slug);

	// Check that the provided post quantity is an integer greater than 1.
	$is_valid_post_quantity = trc_is_non_zero_integer($number_of_posts);

	if( $is_valid_category_slug && $is_valid_post_quantity ) {
		// Provided values are all valid. Real category and valid quantity.

		// Get the category ID.
		$category_id = trc_get_category_id_by_slug($category_slug);

		// Prepare the post criteria.
	  $args = array(
	    'category' => array( $category_id ),
	    'numberposts' => $number_of_posts
	  );

		// Get the posts as an array.
	  $posts = wp_get_recent_posts($args);

	  // Reset the WP Query.
		wp_reset_query();
	}
	else {
		// One or more of the provided values were invalid.
		echo '<p>Error: invalid category slug or quantity.</p>';

		// Invalid parameter values supplied. Return false to indicate error.
		$posts = false;
	}

	// Return the posts array, or false on error.
	return $posts;
}

function trc_shortcode_tests( $atts ) {
	// Runs tests on the Team Rocket Core functions.
	ob_start();

	echo '<p>Hello world! Team Rocket Core test program initiated...</p>';

	echo '<hr>';

	echo '<p>Get recent administrative-meetings:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug('administrative-meetings') );

	echo '<hr>';

	echo '<p>Get recent general meetings:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug('general-meetings') );

	echo '<hr>';

	echo '<p>Get recent events:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug('events', 10) );

	echo '<hr>';

	echo '<p>Get recent projects:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug('projects') );

	echo '<hr>';

	echo '<p>Get recent posts for a non-existent category:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug('no-such-category') );

	echo '<hr>';

	echo '<p>Get recent posts for a category slug given as an integer:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug(5) );

	echo '<hr>';

	echo '<p>Get recent posts by calling the function with no category:</p>';
	trc_display_posts( trc_get_recent_posts_by_category_slug() );

	echo '<hr>';

	return ob_get_clean();
}

add_shortcode( 'team_rocket_core_tests', 'trc_shortcode_tests' );

?>
