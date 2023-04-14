<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/admin
 * @author     Ads.txt Manager <tech@adstxtmanager.com>
 */

class AdstxtManager_Admin {

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

    private $options;

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
        $this->options = array();

    }

	public function display_notice() {
        global $hook_suffix, $pagenow;

        $adstxtmanager_id = get_option('adstxtmanager_id');
        $adstxtmanager_status = get_option('adstxtmanager_status');

        if (!is_int($adstxtmanager_id["adstxtmanager_id"]) || empty($adstxtmanager_id["adstxtmanager_id"])) {
            // delete status option
            delete_option('adstxtmanager_status');
        } else if (in_array($pagenow, array('options-general.php')) && ($_GET['page'] == 'adstxtmanager-setting-admin')) {

            if ((isset($_GET['verify']) && $_GET['verify']) || false === $adstxtmanager_status) {

                $solutionFactory = new AdsTxtManager_Solution_Factory();
                $adsTxtSolution = $solutionFactory->GetBestSolution();
                $adsTxtSolution->SetupSolution();

                $redirect_status = $this->verify_adstxt_redirect();
                $adstxtmanager_status = get_option('adstxtmanager_status');
                $adstxtmanager_status['status'] = $redirect_status;
                update_option('adstxtmanager_status', $adstxtmanager_status);
            }

            if (get_option('adstxtmanager_id') > 0 && false !== $adstxtmanager_status) {
                if ($adstxtmanager_status['status'] === true) {
                    ?>
                    <div class="notice notice-success">
                        <p>Success: Your ads.txt redirect is successfully setup.</p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="notice notice-warning">
                        <p>Oh no! Your ads.txt redirect is not setup correctly!
                            <a href="?page=adstxtmanager-setting-admin&verify=1">Rerun setup and recheck redirection</a>.</p>
                        <?php if (!empty($adstxtmanager_status['message'])) { ?>
                            <hr/><p><?php _e($adstxtmanager_status['message']); ?></p>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
        }

		if ( in_array( $hook_suffix, array( 'plugins.php' ) ) ) {

            $has_issue = false;
            $issue_types = array();
			if( !get_option('permalink_structure') ) {
                $issue_types['type'] = 'permalinks_disabled';
				$has_issue = true;
            }

			if( !is_int($adstxtmanager_id["adstxtmanager_id"]) || empty($adstxtmanager_id["adstxtmanager_id"])) {
                if( $has_issue ) {
                    $issue_types['type'] = $issue_types['type'] . "+" . "no_id";
                } else {
                    $issue_types['type'] = 'no_id';
                    $has_issue = true;
                }
            }

            if( $has_issue ) {
                $args = apply_filters( 'adstxtmanager_view_arguments', $issue_types, 'adstxtmanager-admin' );

                foreach ( $args AS $key => $val ) {
					$$key = $val;
				}

				$file = ADSTXT_MANAGER__PLUGIN_DIR . 'admin/partials/'. 'adstxtmanager-admin-display' . '.php';

				include( $file );
            }
		}
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since 1.0.0
     *
     * @param $links
     *
     * @return array
     */
    public function add_action_links( $links ) {
        $settings_link = array(
            '<a href="options-general.php?page=adstxtmanager-setting-admin">' . __( 'Settings' ) . '</a>',
            '<a href="' . ADSTXT_MANAGER__SITE_LOGIN . '" target="_blank">' . __( 'Login' ) . '</a>',
        );

        return array_merge( $links, $settings_link );
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
		 * defined in AdstxtManager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AdstxtManager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/adstxtmanager-admin.css', array(), $this->version, 'all' );

	}

	    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Ads.txt Manager Settings',
            'manage_options',
            'adstxtmanager-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'adstxtmanager_id' );
        $adstxtmanager_status = get_option('adstxtmanager_status');

        ?>
        <div class="wrap">
            <h1>Ads.txt Manager Settings</h1>
            <?php

            if(empty($this->options['adstxtmanager_id'])) {
                ?>
                <div class="notice notice-info">
                    <p>In order to use Ads.txt Manager, you must enter your Ads.txt Manager ID number below.</p>
                    <p>To find your ID on adstxtmanager.com, <a href="<?php esc_attr_e( ADSTXT_MANAGER__SITE_LOGIN, 'adstxtmanager' ); ?>" target="_blank">click here</a> to login, or create a <a href="<?php esc_attr_e( ADSTXT_MANAGER__SITE, 'adstxtmanager' ); ?>" target="_blank">new account</a>.</p>
                </div>
                <?php
            }

            ?>

            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('adstxtmanager_id_group');
                do_settings_sections('adstxtmanager-setting-admin');
                submit_button('Save Changes', 'primary', 'submit', false);
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'adstxtmanager_status', // Option group
            'adstxtmanager_status', // Option name
            array('type' => 'array')
        );

        register_setting(
            'adstxtmanager_id_group', // Option group
            'adstxtmanager_id', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Ads.txt Manager ID', // Title
            array( $this, 'print_section_info' ), // Callback
            'adstxtmanager-setting-admin' // Page
        );

        add_settings_field(
            'adstxtmanager_id', // ID
            'ID Number', // Title
            array( $this, 'id_number_callback' ), // Callback
            'adstxtmanager-setting-admin', // Page
            'setting_section_id' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['adstxtmanager_id'] ) ) {
            $new_input['adstxtmanager_id'] = absint( $input['adstxtmanager_id'] );
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your Ads.txt Manager ID below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="adstxtmanager_id" name="adstxtmanager_id[adstxtmanager_id]" value="%s" />',
            isset( $this->options['adstxtmanager_id'] ) ? esc_attr( $this->options['adstxtmanager_id']) : ''
        );
    }

    /**
     * @return bool
     */
    public static function verify_adstxt_redirect() {
        global $wp;

        $adstxtmanager_status = get_option('adstxtmanager_status');

        //create endpoint request
        $response = wp_remote_get(home_url($wp->request) . "/ads.txt", array(
            'timeout' => 5,
            'headers' => array('Cache-Control' => 'no-cache'),
        ));

        if (
            !is_wp_error($response)
            && isset($response['http_response'])
            && $response['http_response'] instanceof \WP_HTTP_Requests_Response
            && method_exists($response['http_response'], 'get_response_object')
        ) {
            $location_url = $response['http_response']->get_response_object()->url;

            $url_parse = wp_parse_url($location_url);
            if ($url_parse['host'] == "srv.adstxtmanager.com") {
                $adstxtmanager_status['message'] = "";
                update_option('adstxtmanager_status', $adstxtmanager_status);
                return true;
            } else {
                $adstxtmanager_status['message'] = "The ads.txt is not redirecting to the correct adstxtmanager.com location. Please remove/fix any existing redirections to your <a href=\"" . home_url($wp->request) . "/ads.txt\" target=\"_blank\">ads.txt</a> file.";
                update_option('adstxtmanager_status', $adstxtmanager_status);
                return false;
            }
        }

        $adstxtmanager_status['message'] = "Unable to verify your ads.txt redirection.";
        update_option('adstxtmanager_status', $adstxtmanager_status);
        return false;
    }

}
