<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ads.txt Manager
 * @subpackage Ads.txt Manager/includes
 * @author     Ads.txt Manager <tech@adstxtmanager.com>
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-adstxtmanager-solution-factory.php';
class AdstxtManager_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$solutionFactory = new AdsTxtManager_Solution_Factory();
		$adsTxtSolution = $solutionFactory->GetBestSolution();
		$adsTxtSolution->TearDownSolution();
	}

}
