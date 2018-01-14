<?php

/*
* Do individual Time-based Tasks
*
*/
if ( ! wp_next_scheduled( 'delete_expired_events' ) ) {
  wp_schedule_event( time(), 'hourly', 'my_task_hook' );
}

add_action( 'delete_expired_events', 'delete_expired_event' );

function send_mail() {
  wp_mail( 'my.email@provider.com', 'Automatisches Email', 'Hier erscheint die Emain Nachricht.');
}

/*
*   Removes expired static events from database
*/
function delete_expired_event(){
  global $wpdb;
  $results = $wpdb->get_results ( "SELECT * FROM wp_static_events" );

  foreach ( $results as $DBevent )   {
      try{

      if( isDateInPast($DBevent->event_date) )
        $wpdb->delete( 'wp_static_events', array( 'event_date' => $DBevent->event_date ) );

      }catch (Exception $e) {
          echo "<script>console.log( 'Couldn't delete event ".$DBevent->id." from database: " . $e->getMessage() . "' );</script>";
      }
  }
  send_mail();
}
