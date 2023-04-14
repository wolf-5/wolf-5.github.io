<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/public
 * @author     Ads.txt Manager <tech@adstxtmanager.com>
 */
class AdstxtManager_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function handle_adstxt() {
		global $wp;

        $request = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : false;
        if ('/ads.txt' == $request && get_option('permalink_structure')) {
            $adstxtmanager_id = get_option('adstxtmanager_id');

            if (!empty($adstxtmanager_id["adstxtmanager_id"]) && is_int($adstxtmanager_id["adstxtmanager_id"])) {
                $domain = home_url($wp->request);
                $domain = parse_url($domain);
                $domain = $domain['host'];
                $domain = preg_replace('#^(http(s)?://)?w{3}\.#', '$1', $domain);
                header("HTTP/1.1 301 Moved Permanently");
                header('Location: https://srv.adstxtmanager.com/' . $adstxtmanager_id["adstxtmanager_id"] . '/' . $domain);
                exit();
            }
        }
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

	}

}
