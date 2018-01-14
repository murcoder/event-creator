<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.murdesign.at
 * @since      1.0.0
 *
 * @package    Event_Creator
 * @subpackage Event_Creator/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

     <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
     <a class="button button-primary" target="_blank" href="<?php echo get_site_url(); ?>/termine"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> zur Webseite</a>

     <div class="flexbox">

     <div class="event_form">
        <form method="post" name="event_creator_add" action="">

        <?php
          //Output nonce, action, and option_page fields for a settings page.
          //Please note that this function must be called inside of the form tag for the options page.
          settings_fields($this->plugin_name);
          do_settings_sections($this->plugin_name);
        ?>

        <!-- Use hidden field to select a form -->
        <input type="hidden" name="<?php echo $this->plugin_name; ?>-add_event" value="Y">
        <input type="hidden"  id="<?php echo $this->plugin_name; ?>-id" name="<?php echo $this->plugin_name; ?>_id" />

        <?php
          global $wpdb;
          $table_name = $wpdb->prefix . 'static_events_tags';
          $tags = $wpdb->get_results ( "SELECT * FROM $table_name" );
         ?>
        <fieldset>
          <label><?php _e('Kategorie', $this->plugin_name); ?></label><br />
            <select name="<?php echo $this->plugin_name; ?>_tag">
              <?php foreach ( $tags as $tag )   { ?>
              <option value="<?php echo $tag->tag; ?>"><?php echo $tag->tag; ?></option>
              <?php } ?>
          </select>
        </fieldset>

        <fieldset>
              <label><?php _e('Datum', $this->plugin_name); ?></label><br>
              <input required type="date" class="event-date-input" id="<?php echo $this->plugin_name; ?>-date" name="<?php echo $this->plugin_name; ?>_date" placeholder="tt-mm-yyyy"/>
              <input autocomplete="off" type="text" class="event-time-input" id="<?php echo $this->plugin_name; ?>-time" name="<?php echo $this->plugin_name; ?>_time" placeholder="(optional) Uhrzeit '10:00' "/>
        </fieldset>

        <fieldset>
              <label><?php _e('Text', $this->plugin_name); ?></label><br>
              <textarea required rows="3" cols="44" class="event-content-input" id="<?php echo $this->plugin_name; ?>-content" name="<?php echo $this->plugin_name; ?>_content" placeholder="Inhalt der Verantstaltung"></textarea>
        </fieldset>

        <fieldset>
              <label><?php _e('Vortragender', $this->plugin_name); ?></label><br>
              <input autocomplete="off" type="text" class="event-speaker-input" id="<?php echo $this->plugin_name; ?>-speaker" name="<?php echo $this->plugin_name; ?>_speaker" placeholder="(optional) Frau Dr. Musterfrau"/>
        </fieldset>

        <fieldset>
              <label><?php _e('Land', $this->plugin_name); ?></label><br>
              <select name="<?php echo $this->plugin_name; ?>_country">
                <option value="AT">Österreich</option>
                <option value="DE">Deutschland</option>
                <option value="CH">Schweiz</option>
              </select>
        </fieldset>

        <fieldset>
              <label><?php _e('Ort', $this->plugin_name); ?></label><br>
              <input required autocomplete="off" type="text" class="event-location-input" id="<?php echo $this->plugin_name; ?>-location" name="<?php echo $this->plugin_name; ?>_location" placeholder="Bahnhof Oberndorf, Oberndorfstr. 20, 4020 Hartberg"/>
        </fieldset>
        <fieldset>
              <label><?php _e('Datei', $this->plugin_name); ?></label><br>
              <input autocomplete="off" type="text" class="event-file-input" id="<?php echo $this->plugin_name; ?>-file" name="<?php echo $this->plugin_name; ?>_file" placeholder="(optional) https://selpers.com/files/uploads/2017/09/Flyer_Brustkrebstag_10-2017.pdf"/>
        </fieldset>

        <fieldset>
              <label><?php _e('Link', $this->plugin_name); ?></label><br>
              <input autocomplete="off" type="text" class="event-url-input" id="<?php echo $this->plugin_name; ?>-url" name="<?php echo $this->plugin_name; ?>_url" placeholder="(optional) www.testlink.at"/>
        </fieldset>

        <?php submit_button(__('Veranstaltung erstellen', $this->plugin_name), 'primary','submit', TRUE); ?>
        </form>
      </div>



  <!-- LIST ALL STATIC EVENTS -->
  <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'static_events';
    $results = $wpdb->get_results ( "SELECT * FROM $table_name" );
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
   ?>

    <div class="event_list">
      <table id="speedy-plugin-table"  summary="This table lists all static events">
      	<caption><?php _e('Alle statischen Termine (' . $rowcount . ')', $this->plugin_name); ?></caption>

      	<thead>
      	<tr class="odd">
      		<th scope="col" abbr="Category" onclick="sortTable(0)"><?php _e('Kategorie', $this->plugin_name); ?></th>
      		<th scope="col" abbr="Date" onclick="sortTable(1)"><?php _e('Datum', $this->plugin_name); ?></th>
          <th scope="col" abbr="Country" onclick="sortTable(2)"><?php _e('Land', $this->plugin_name); ?></th>
      		<th scope="col" abbr="Content" onclick="sortTable(3)"><?php _e('Inhalt', $this->plugin_name); ?></th>
      		<th scope="col" abbr="Speaker" onclick="sortTable(4)"><?php _e('Vortragender', $this->plugin_name); ?></th>
          <th scope="col" abbr="Location" onclick="sortTable(5)"><?php _e('Ort', $this->plugin_name); ?></th>
          <th scope="col" abbr="Link"><?php _e('Link', $this->plugin_name); ?></th>
      	</tr>
      	</thead>

      	<tbody>
        <?php
        foreach ( $results as $event )   {
        ?>
         	<tr>
        		<td scope="row" class="column1"><?php echo $event->tag;?></td>
            <td scope="row" class="column2"><?php echo $event->event_date;?>, <?php echo $event->event_time;?></td>
            <td scope="row" class="column3"><?php echo $event->event_country;?></td>
            <td scope="row" class="column4"><?php echo $event->event_content;?></td>
            <td scope="row" class="column5"><?php echo $event->event_speaker;?></td>
            <td scope="row" class="column6"><?php echo $event->event_location;?></td>
            <td scope="row" class="column7"><a target="_blank" href="<?php echo $event->url;?>"><?php echo $event->url;?></a></td>
            <td hidden scope="row" name="event_id"><?php echo $event->id;?></td>
            <td scope="row" class="column8"><form class="delete" name="event_creator_delete" action="" method="post">
                <input type="hidden" name="<?php echo $this->plugin_name; ?>-remove_event" value="Y">
                <input type="hidden" name="<?php echo $this->plugin_name; ?>_id" value="<?php echo $event->id;?>">  <!-- get id -->
                <?php submit_button( __( 'X', $this->plugin_name ), 'delete button-primary', FALSE ); ?></form>
            </td>
        	</tr>
          <?php } ?>
      	</tbody>
      </table>
    </div>
  </div>
</div>
<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("speedy-plugin-table");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc";
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
