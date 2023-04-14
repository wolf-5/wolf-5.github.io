<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.adstxtmanager.com/
 * @since             1.0.0
 * @package           Ads.txt Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Ads.txt Manager
 * Description:       This plugin allows AdsTxtManger.com to easily manage your ads.txt and integrate with your WordPress site.
 * Version:           1.0.9
 * Author:            Ads.txt Manager <tech@adstxtmanager.com>
 * Author URI:        https://www.adstxtmanager.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       adstxtmanager
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
define( 'ADSTXT_MANAGER_VERSION', '1.0.9' );
define( 'ADSTXT_MANAGER__PLUGIN_NAME', 'Ads.txt Manager' );
define( 'ADSTXT_MANAGER__PLUGIN_SLUG', dirname( plugin_basename( __FILE__ ) ) );
define( 'ADSTXT_MANAGER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADSTXT_MANAGER__PLUGIN_FILE', plugin_basename( __FILE__ ) );

define( 'ADSTXT_MANAGER__SITE', 'https://www.adstxtmanager.com/' );
define( 'ADSTXT_MANAGER__SITE_LOGIN', 'https://svc.adstxtmanager.com/auth/login' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-adstxtmanager-activator.php
 */
function activate_adstxtmanager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-adstxtmanager-activator.php';
	AdstxtManager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-adstxtmanager-deactivator.php
 */
function deactivate_adstxtmanager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-adstxtmanager-deactivator.php';
	AdstxtManager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_adstxtmanager' );
register_deactivation_hook( __FILE__, 'deactivate_adstxtmanager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-adstxtmanager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_adstxtmanager() {

	$plugin = new AdstxtManager();
	$plugin->run();

}
run_adstxtmanager();
