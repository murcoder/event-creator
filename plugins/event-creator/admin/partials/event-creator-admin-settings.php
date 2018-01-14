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
    <div class="flexbox">
    <div class="event_creator_new_tag">
        <?php
          //Output nonce, action, and option_page fields for a settings page.
          //Please note that this function must be called inside of the form tag for the options page.
          settings_fields($this->plugin_name);
          do_settings_sections($this->plugin_name);
        ?>

      <h2><?php _e('Kategorien', $this->plugin_name);  ?></h2>

        <!-- create a new Tag -->
      <form method="post" name="event_creator_add_tag" action="">
          <fieldset>
                <p><?php _e('Neue Kategorie erstellen', $this->plugin_name);  ?></p>
                <input type="hidden" name="<?php echo $this->plugin_name; ?>-add_tag" value="Y">
                <input class="event-new-tag" id="<?php echo $this->plugin_name; ?>-newTag" name="newTag" placeholder="Neue Kategorie" />
          </fieldset>

        <?php
        //Usage: submit_button( $text, $type, $name, $wrap, $other_attributes );
        submit_button(__('Erstellen', $this->plugin_name), 'primary','submit', TRUE);
        ?>
      </form>


          <?php
          global $wpdb;
          $table_name = $wpdb->prefix . 'static_events_tags';
          $tags = $wpdb->get_results ( "SELECT * FROM $table_name" );
           ?>

          <div class="event_list settings-list">
          <table summary="This table lists all static events">
          	<caption><?php _e('Alle Kategorien', $this->plugin_name); ?></caption>

          	<tbody>
            <?php
            foreach ( $tags as $tag )   {
            ?>
             	<tr>
                <th hidden scope="row" name="event_id"><?php echo $event->id;?></th>
            		<th scope="row" class="column1"><?php echo $tag->tag; ?></th>
                <th scope="row" class="column7"><form class="delete" name="tag_delete" action="" method="post">
                    <input type="hidden" name="<?php echo $this->plugin_name; ?>-remove_tag" value="Y">
                    <input type="hidden" name="<?php echo $this->plugin_name; ?>_tag_id" value="<?php echo $tag->id;?>">  <!-- get id -->
                    <?php submit_button( __( 'X', $this->plugin_name ), 'delete button-primary', FALSE ); ?></form>
                </th>
            	</tr>
              <?php } ?>
          	</tbody>
          </table>
        </div>



    </div>



    <div class="event_creator_options">
      <h2><?php _e('Einstellungen', $this->plugin_name);  ?></h2>

      <form method="post" name="event_creator_options" action="">

        <?php
          //Grab all options
          $options = get_option($this->plugin_name);

          $debug = $options['debug'];
          $newTag = $options['newTag'];
        ?>

        <!-- turn on error messages -->
        <fieldset>
          <legend class="screen-reader-text">
              <span>Turn on more detailed messages</span>
          </legend>
          <label for="<?php echo $this->plugin_name; ?>-debug">
              <input type="checkbox" id="<?php echo $this->plugin_name; ?>-debug" name="<?php echo $this->plugin_name; ?>[debug]" value="1" <?php checked($debug, 1); ?> />
              <span><?php esc_attr_e('Debug modus aktivieren', $this->plugin_name); ?></span>
          </label>
        </fieldset>
        <?php
        //Usage: submit_button( $text, $type, $name, $wrap, $other_attributes );
        submit_button(__('Sichern', $this->plugin_name), 'primary','submit', TRUE);
        ?>
      </form>
  </div>
</div>
</div>
