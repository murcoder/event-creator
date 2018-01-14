<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.murdesign.at
 * @since      1.0.0
 *
 * @package    Event_Creator
 * @subpackage Event_Creator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Event_Creator
 * @subpackage Event_Creator/admin
 * @author     Christoph Murauer <christoph.murauer@speedy-space.com>
 */
class Event_Creator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->wp_event_creator_options = get_option($this->plugin_name);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Event_Creator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Creator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/event-creator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Event_Creator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Creator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/event-creator-admin.js', array( 'jquery' ), $this->version, false );

	}



	/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */

public function add_plugin_admin_menu() {

    /*
     * Add a settings page for this plugin to the Settings menu.
     *
     * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
     *
     *        Administration Menus: http://codex.wordpress.org/Administration_Menus
     *
     */
    add_options_page( 'Event Creator', 'Event Creator', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
    );
}


public function custom_add_menu() {

        add_menu_page( 'Event Creator', 'Event Creator', 'manage_options', 'event-creator-dashboard', array($this, 'display_dashboard_page'), plugins_url('img/event-logo.png', __FILE__),'2.2.9');

        add_submenu_page( 'event-creator-dashboard', 'Event Creator' . ' - Event erstellen', 'Event erstellen', 'manage_options', 'event-creator-dashboard', array($this, 'display_dashboard_page'));

        add_submenu_page( 'event-creator-dashboard', 'Event Creator' . ' - Konfiguration', 'Konfiguration', 'manage_options', 'event-creator-settings', array($this, 'display_plugin_setup_page'));
    }



 /**
 * Add settings action link to the plugins page.
 *
 * @since    1.0.0
 */

public function add_action_links( $links ) {
	    /*
	    *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	    */
	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
public function display_plugin_setup_page() {
	    include_once( 'partials/event-creator-admin-settings.php' );
	}

	/**
	 * Render the dashboard page for this plugin.
	 *
	 * @since    1.0.0
	 */
public function display_dashboard_page() {
	    include_once( 'partials/event-creator-admin-dashboard.php' );
	}

	/**
	*
	* admin/class-wp-cbf-admin.php
	*
	**/
public function validate($input) {
	    // All checkboxes inputs
	    $valid = array();

	    //Cleanup
	    $valid['debug'] = (isset($input['debug']) && !empty($input['debug'])) ? 1 : 0;
	    $valid['newTag'] = esc_url($input['newTag']);

	    return $valid;
	 }


	 /**
	 *
	 * admin/class-wp-cbf-admin.php
	 *
	 **/
public function options_update() {
			//register a setting and control the input with a callback function
	    register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
			//$_POST[$this->plugin_name . "_newTag"];
	  }




		/**
		* Add an event to the database
		*
		* @since    1.0.0
		*/
public function add_event_to_DB() {
				global $wpdb;

				//choose function by hidden field
		    $hidden_field_name = $this->plugin_name . "-add_event";

				// name-fields of html input
				$tag =  $this->plugin_name . "_tag";
			  $date =  $this->plugin_name . "_date";
			  $event_time =  $this->plugin_name . "_time";
				$content =  $this->plugin_name . "_content";
				$speaker =  $this->plugin_name . "_speaker";
				$country =  $this->plugin_name . "_country";
		    $location =  $this->plugin_name . "_location";
				$url =  $this->plugin_name . "_url";
				$file =  $this->plugin_name . "_file";



		    // See if the user has posted us some information
		    // If they did, this hidden field will be set to 'Y'
		    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

					//convert date to database-format
					$time = strtotime($_POST[$date]);
					$dateDBFormat = date('Y-m-d',$time);

					//Add user input to database
						$wpdb->insert( $wpdb->prefix . 'static_events', array(
							'added' => current_time( 'mysql' ),
							'tag' => $_POST[$tag],
							'event_date' => $dateDBFormat,
							'event_time' => $_POST[$event_time],
							'event_content' => $_POST[$content],
							'event_speaker' => $_POST[$speaker],
							'event_country' => $_POST[$country],
							'event_location' => $_POST[$location],
							'file_url' => $_POST[$file],
			        'url' => $_POST[$url]
			    ));

					//Check for database errors
					if($wpdb->last_error !== ''){
						//Print all database errors
						$this->db_print_error();
					}else{
		        // Put an options updated message on the screen
						$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
						?>
						<div class="updated">
							<p><strong><?php _e('Event added!', $this->plugin_name ); ?></strong><br />
								<?php echo ($this->wp_event_creator_options['debug'] ? "<code>$query</code>" : ""); ?>
							</p>
						</div>
						<?php
					}
		    }

		}




		/**
		* Remove an event out of the database
		*
		* @since    1.0.0
		*/
public function remove_event_from_DB(){


					global $wpdb;

					//choose function by hidden field
					$hidden_field_name = $this->plugin_name . "-remove_event";
					$id_field_name = $this->plugin_name . '_id';

					if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

							$wpdb->delete(  $wpdb->prefix . 'static_events', array( 'ID' => $_POST[$id_field_name] )  );
							//echo "<script>console.log( 'Debug Objects: " . $_POST[$id_field_name] . "' );</script>";

							//Check for database errors
							if($wpdb->last_error !== ''){
								//Print all database errors
								$this->db_print_error();
							}else{
								// Put an options updated message on the screen
								$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
								?>
								<div class="updated">
									<p><strong><?php _e('Event deleted!', $this->plugin_name ); ?></strong><br />
										<?php echo ($this->wp_event_creator_options['debug'] ? "<code>$query</code>" : ""); ?>
									</p>
								</div>
								<?php
							}
					}
}





/**
* Adds a new category to database
*
* @since    1.0.0
*/
public function add_tag_to_DB(){

		global $wpdb;
		$table_name = $wpdb->prefix . 'static_events_tags';
		$hidden_field_name = $this->plugin_name . "-add_tag";

		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

			$wpdb->insert(
				$table_name,
				array(
					'added' => current_time( 'mysql' ),
					'tag' => $_POST['newTag']
				)
			);

			//Check for database errors
			if($wpdb->last_error !== ''){
				//Print all database errors
				$this->db_print_error();
			}else{
				// Put an options updated message on the screen
				$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
				?>
				<div class="updated">
					<p><strong><?php _e('Tag created!', $this->plugin_name ); ?></strong><br />
						<?php echo ($this->wp_event_creator_options['debug'] ? "<code>$query</code>" : ""); ?>
					</p>
				</div>
				<?php
			}
		}
}



/**
* Removes an category of the database
*
* @since    1.0.0
*/
public function remove_tag_from_DB(){


			global $wpdb;

			//choose function by hidden field
			$hidden_field_name = $this->plugin_name . "-remove_tag";
			$id_field_name = $this->plugin_name . '_tag_id';

			if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

					$wpdb->delete(  $wpdb->prefix . 'static_events_tags', array( 'ID' => $_POST[$id_field_name] )  );

					//Check for database errors
					if($wpdb->last_error !== ''){
						//Print all database errors
						$this->db_print_error();
					}else{
						// Put an options updated message on the screen
						$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
						?>
						<div class="updated">
							<p><strong><?php _e('Tag deleted!', $this->plugin_name ); ?></strong><br />
								<?php echo ($this->wp_event_creator_options['debug'] ? "<code>$query</code>" : ""); ?>
							</p>
						</div>
						<?php
					}
			}
}



/**
* Print messages for database-errors
*
* @since    1.0.0
*/
public function db_print_error(){

		    global $wpdb;

		    if($wpdb->last_error !== ''){

		        $str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
						$query = "";

						if($this->wp_event_creator_options['debug'])
		        	$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );


		        echo "<div class='error'><p><strong>WordPress database error:</strong> [$str]<br /><code>$query</code></p></div>";

		    }

		}

}
