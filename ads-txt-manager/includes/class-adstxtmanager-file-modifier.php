<?php

class AdsTxtManager_File_Modifier implements iAdsTxtManager_Solution {

    private function determineRootPath() {
        return get_home_path();
    }

    private function modifiedAdsTxtFileName() {
        return $this->determineRootPath() . "ads-txt-orig.txt";
    }

    private function origAdsTxtFileName() {
        return $this->determineRootPath() . "ads.txt";
    }

    public function SetupSolution() {
        //Do we have an ads.txt file? rename the file to ads-txt-orig.txt
        //Get path to cache folder and insert out htaccess file or modify current htaccess file
        $filePath = $this->origAdsTxtFileName();
        $newFilePath = $this->modifiedAdsTxtFileName();

	    if(empty($filePath) || !file_exists($filePath)) {
            return;
        }

        $renameSuccess = rename($filePath, $newFilePath);
	    if ($renameSuccess == false) {
            $message = "Failed to rename existing ads.txt file.";
        }

        $redirect_status = AdstxtManager_Admin::verify_adstxt_redirect();
        $adstxtmanager_status = get_option('adstxtmanager_status');
        $adstxtmanager_status['status'] = $redirect_status;
        if (!empty($message)) {
            $adstxtmanager_status['message'] = $message;
        }
        update_option( 'adstxtmanager_status', $adstxtmanager_status );

    }

    public function TearDownSolution() {
        //Do we have an ads-txt-orig.txt file? restore that to ads.txt
        $modifiedFilePath = $this->modifiedAdsTxtFileName();
        $origFilePath = $this->origAdsTxtFileName();

        if(empty($modifiedFilePath ) || !file_exists($modifiedFilePath )) {
            return;
        }

        $renameSuccess = rename($modifiedFilePath , $origFilePath);

        delete_option('adstxtmanager_status');
    }
}
