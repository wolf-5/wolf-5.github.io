<?php

interface iAdsTxtManager_Solution {
    public function SetupSolution();
    public function TearDownSolution();
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-adstxtmanager-htaccess-modifier.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-adstxtmanager-file-modifier.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-adstxtmanager-empty-solution.php';

class AdsTxtManager_Solution_Factory {

    public function GetBestSolution() {

        $emptyModifier = new AdsTxtManager_Empty_Solution();
        //Do we have a ads txt manager id?
        $adstxtmanager_id = get_option('adstxtmanager_id');
        if( !is_int($adstxtmanager_id["adstxtmanager_id"]) || $adstxtmanager_id["adstxtmanager_id"] == 0 ) {
            //we don't have an adstxtmanager_id, lets return the empty solution
            return $emptyModifier;
        }

        //If we have apache, lets modify the sites htaccess file
        if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false ) {
            //return htaccess solution
            $htaccessModifier = new AdstxtManager_HTACCESS_Modifier();
            return $htaccessModifier;
        } else {
            //return file modification solution
            $fileModifier = new AdsTxtManager_File_Modifier();
            return $fileModifier;
        }
    }
}
