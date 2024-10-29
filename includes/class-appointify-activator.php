<?php

/**
 * Fired during plugin activation
 *
 * @link       appointify.app
 * @since      1.0.0
 *
 * @package    Appointify
 * @subpackage Appointify/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Appointify
 * @subpackage Appointify/includes
 * @author     appointify
 */
class Appointify_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		
			// create the custom table
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'holidays';
			$charset_collate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE $table_name ( `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `date` date NOT NULL,
			  `start_time` time NOT NULL,
			  `end_time` time NOT NULL,
			  `isDelete` int(11) NOT NULL,
			  `dateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			) $charset_collate;";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );


			$table_nameuserdata = $wpdb->prefix . 'user_data';
			
			$sqluserdata = "CREATE TABLE $table_nameuserdata ( `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `token_data` text NOT NULL,
			  `calendar_id` varchar(255) NOT NULL,
			  `status` tinyint(1) NOT NULL DEFAULT '1',
			  `dateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			) $charset_collate;";
			
			dbDelta( $sqluserdata );



			$table_nametbl_events = $wpdb->prefix . 'tbl_events';
			
			$sqltbl_events = "CREATE TABLE $table_nametbl_events ( `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			  `start` datetime NOT NULL,
			  `end` datetime DEFAULT NULL,
			  `calendar_id` varchar(255) NOT NULL,
			  `event_id` varchar(255) NOT NULL,
			  `google_status` int(1) NOT NULL DEFAULT '1'
			) $charset_collate;";
			
			dbDelta( $sqltbl_events );

			$calendar_working_hr = get_option( 'calendar_working_hr');

				if( empty($calendar_working_hr) ){
					$working_hr_val = array (
						  1 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  ),
						  2 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  ),
						  3 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '16:00',
						  ),
						  4 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  ),
						  5 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  ),
						  6 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  ),
						  7 => array (
						    'is_working' => 'on',
						    'start_time' => '09:00',
						    'end_time' => '17:00',
						    'is_break' => 'on',
						    'break_start' => '14:00',
						    'break_end' => '15:00',
						  )
						);
					update_option( 'calendar_working_hr',  json_encode($working_hr_val) );
				}


	}

}
