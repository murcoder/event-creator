<?php

/**
 * Fired during plugin activation
 *
 * @link       www.murdesign.at
 * @since      1.0.0
 *
 * @package    Event_Creator
 * @subpackage Event_Creator/includes
 */





/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Event_Creator
 * @subpackage Event_Creator/includes
 * @author     Christoph Murauer <christoph.murauer@speedy-space.com>
 */
class Event_Creator_Activator {


		/**
	  * The current version of the database.
	  *
	  * @since    1.0.0
	  * @access   protected
	  * @var      string    $version    The current version of the plugin.
	  */
	 static $db_version = '1.0.0';

	 //Call the static version variable
	 public static function getDatabaseVersion() {
	     return self::$db_version;
	 }

	public static function activate() {

					//Create database table
					global $wpdb;
				  $plugin_version = get_option( 'event_creator_version', '1.0.0' );
					$charset_collate = $wpdb->get_charset_collate();

					//Create table 'prefix_static_events'
					$table_name = $wpdb->prefix . 'static_events';
					$sql = "CREATE TABLE $table_name (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						tag varchar(255) NOT NULL,
						event_date date DEFAULT '0000-00-00' NOT NULL,
						event_time varchar(255) NOT NULL,
						event_content text NOT NULL,
						event_speaker varchar(255) NOT NULL,
						event_country text NOT NULL,
						event_location text NOT NULL,
						file_url text NOT NULL,
						url text NOT NULL,
						added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						PRIMARY KEY  (id)
					) $charset_collate;";

					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					dbDelta( $sql );
					add_option( 'event_creator_db_version', Event_Creator_Activator::getDatabaseVersion() );


					if ( Event_Creator_Activator::getDatabaseVersion() != $plugin_version ) {
						//Insert UPDATES here
						$sql = "CREATE TABLE $table_name (
							id mediumint(9) NOT NULL AUTO_INCREMENT,
							tag varchar(255) NOT NULL,
							event_date date DEFAULT '0000-00-00' NOT NULL,
							event_time varchar(255) NOT NULL,
							event_content text NOT NULL,
							event_speaker varchar(255) NOT NULL,
							event_country text NOT NULL,
							event_location text NOT NULL,
							file_url text NOT NULL,
							url text NOT NULL,
							added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
							PRIMARY KEY  (id)
						) $charset_collate;";

						require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
						dbDelta( $sql );
					  update_option( 'event_creator_db_version', Event_Creator_Activator::getDatabaseVersion() );

					}




					//Create table 'prefix_static_events_tags'
					$table_name = $wpdb->prefix . 'static_events_tags';
					$sql = "CREATE TABLE $table_name (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						tag varchar(255) NOT NULL,
						added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						PRIMARY KEY  (id)
					) $charset_collate;";

					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					dbDelta( $sql );

					if ( Event_Creator_Activator::getDatabaseVersion() != $plugin_version ) {
						//Insert UPDATES here
						$sql = "CREATE TABLE $table_name (
							id mediumint(9) NOT NULL AUTO_INCREMENT,
							tag varchar(255) NOT NULL,
							added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
							PRIMARY KEY  (id)
						) $charset_collate;";

						require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
						dbDelta( $sql );
					  update_option( 'event_creator_db_version', Event_Creator_Activator::getDatabaseVersion() );

					}


					//TODO Add tag array
					Event_Creator_Activator::event_creator_install_data($table_name);

		}


		//Database tester
		public static function event_creator_install_data($table_name) {
			global $wpdb;

			$tags = array(
			  'Allgemein'     => 'Allgemein',
			  'Krebs'      => 'Krebs',
			  'Prostata'   => 'Prostatakrebs',
			  'Myelom'   => 'Myelom und Lymphom',
			  'FrauenKrebs' => 'Krebs bei Frauen',
			  'Psyche'     => 'Psychische Gesundheit',
				'Darm' => 'Magen und Darm',
				'Brust' => 'Brustkrebs'
			);


			foreach ( $tags as $tag ){

				$duplicate = $wpdb->get_var(
												$wpdb->prepare(
														"SELECT tag FROM ".$table_name."
														WHERE tag = %d",
														$tag
												)
										);


				if ( $duplicate <= 0 ){
					$wpdb->insert(
						$table_name,
						array(
							'added' => current_time( 'mysql' ),
							'tag' => $tag
						)
					);
				}
			}
		}

}
