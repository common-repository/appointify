<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Appointify
 * Plugin URI:        https://appointify.app
 * Description:       Most scheduling tools put the burden on the recipient. 
Appointify makes it easy for both parties to find 
the best time to meet—in an instant.
 * Version:           1.0.8
 * Author:            Appointify
 * Author URI:        appointify.app
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       appointify
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'APPOINTIFY_VERSION', '1.0.8' );
define( 'APPOINTIFY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-appointify-activator.php
 */
function appointify_activate($network_wide) {
	global $wpdb;
	require_once APPOINTIFY_PLUGIN_PATH . 'includes/class-appointify-activator.php';
	if ( is_multisite() && $network_wide ) {
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            Appointify_Activator::activate();
            restore_current_blog();
        }
    } else {
        Appointify_Activator::activate();
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-appointify-deactivator.php
 */
function appointify_deactivate() {
	require_once APPOINTIFY_PLUGIN_PATH . 'includes/class-appointify-deactivator.php';
	Appointify_Deactivator::appointify_deactivate();
}

register_activation_hook( __FILE__, 'appointify_activate' );
register_deactivation_hook( __FILE__, 'appointify_deactivate' );

/**
 * 
 * Load text-domain
 *
 * @since 1.0.0
 */
function appointify_load_textdomain() {
    load_plugin_textdomain( 'appointify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'appointify_load_textdomain' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require APPOINTIFY_PLUGIN_PATH . 'includes/class-appointify.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function appointify_run() {

	$plugin = new Appointify();
	$plugin->run();

}
appointify_run();
