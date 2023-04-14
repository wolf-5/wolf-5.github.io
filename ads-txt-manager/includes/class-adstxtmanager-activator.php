<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/includes
 * @author     Ads.txt Manager <tech@adstxtmanager.com>
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-adstxtmanager-solution-factory.php';
class AdstxtManager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$solutionFactory = new AdsTxtManager_Solution_Factory();
		$adsTxtSolution = $solutionFactory->GetBestSolution();
		$adsTxtSolution->SetupSolution();
	}

}
