<?php
/*
 	AJAX - Pass the admin.ajax url to javascript
*/
// Register the script
wp_enqueue_script( 'filter_events_ajax',get_stylesheet_directory_uri() . '/js/event_filter.js', array( 'jquery' ) );

// Localize the script with new data
wp_localize_script( 'filter_events_ajax', 'event_filter_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );


// Setup Ajax action hook
add_action( 'wp_ajax_filter_events', 'filterEvents');
function filterEvents() {
  //Custom functions here
    exit;
}
