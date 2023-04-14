<?php

class AdstxtManager_HTACCESS_MODIFIER implements iAdsTxtManager_Solution {

    public function SetupSolution() {
        $this->GenerateHTACCESSFile();

        // setup file modifier as backup
        $fileModifier = new AdsTxtManager_File_Modifier();
        $fileModifier->SetupSolution();

        $redirect_status = AdstxtManager_Admin::verify_adstxt_redirect();
        $adstxtmanager_status = get_option('adstxtmanager_status');
        $adstxtmanager_status['status'] = $redirect_status;
        update_option( 'adstxtmanager_status', $adstxtmanager_status );
    }

    public function TearDownSolution() {
        $this->RemoveHTACCESSFile();

        $fileModifier = new AdsTxtManager_File_Modifier();
        $fileModifier->TearDownSolution();

        delete_option('adstxtmanager_status');
    }

    private function determineHTACCESSRootPath() {
        return get_home_path();
    }

    public function GenerateHTACCESSFile() {
        global $wp, $wp_filesystem;
        $message = '';

        //Get path to cache folder and insert out htaccess file or modify current htaccess file
        $filePath = $this->determineHTACCESSRootPath() . ".htaccess";
	    if(empty($filePath) || !file_exists($filePath) || !is_readable($filePath) || !is_writable($filePath)) {
            return;
        }
        //Make sure we start clean
        self::RemoveHTACCESSFile();

        $content = $wp_filesystem->get_contents($filePath);

        $adstxtmanager_id = get_option('adstxtmanager_id');
        $domain = "";
        if( is_int($adstxtmanager_id["adstxtmanager_id"]) && $adstxtmanager_id["adstxtmanager_id"] !== 0 ) {
            $domain = home_url( $wp->request );
            $domain = parse_url($domain);
            $domain = $domain['host'];
            $domain = preg_replace('#^(http(s)?://)?w{3}\.#', '$1', $domain);
        }

        if( $domain === "" ) {
            //We don't have a domain, don't fudge this up
            return;
        }

        $atmContent = array("#BEGIN_ADSTXTMANAGER_HTACCESS_HANDLER",
                            '<IfModule mod_rewrite.c>',
                            'Redirect 301 /ads.txt ' . 'https://srv.adstxtmanager.com/'. $adstxtmanager_id["adstxtmanager_id"] . '/' . $domain,
                            '</IfModule>',
                            "#END_ADSTXTMANAGER_HTACCESS_HANDLER");

        $atmFinalContent = implode("\n", $atmContent);
        $modifiedContent = $atmFinalContent . "\n" .$content;

        $success = $wp_filesystem->put_contents($filePath, $modifiedContent);
        @clearstatcache();
        //$success = file_put_contents($filePath, $modifiedContent);

        if (!$success) {
            $message = "We failed to modify your HTACCESS file.";
        }

        if (!empty($message)) {
            $adstxtmanager_status = get_option('adstxtmanager_status');
            $adstxtmanager_status['message'] = $message;
            update_option('adstxtmanager_status', $adstxtmanager_status);
        }
    }

    public function RemoveHTACCESSFile() {
        //Get path to cache folder and din htaccess file,
        //see if we are the only code in the file and then remove it
        $filePath = $this->determineHTACCESSRootPath() . ".htaccess";

        if(empty($filePath) || !file_exists($filePath) || !is_writable($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $lineContent = preg_split("/\r\n|\n|\r/", $content);
        //Find all text between #ADSTXTMANAGER_INTEGRATION_MODIFICATION
        $beginAtmContent = 0;
        $endAtmContent = 0;
        foreach( $lineContent as $key => $value ) {
            if( $value == "#BEGIN_ADSTXTMANAGER_HTACCESS_HANDLER" ) {
                $beginAtmContent = $key;
            } elseif ( $value == "#END_ADSTXTMANAGER_HTACCESS_HANDLER") {
                $endAtmContent = $key;
            }
        }

        if( $endAtmContent == 0 ) {
            //Don't do anything if we couldn't find an end to our code
            return;
        }

        for( $i = $beginAtmContent; $i <= $endAtmContent; $i++ ) {
            unset($lineContent[$i]);
        }

        $modifiedContent = implode("\n", $lineContent);
        //Dump out to htaccess file
        file_put_contents($filePath, $modifiedContent);
    }



}
