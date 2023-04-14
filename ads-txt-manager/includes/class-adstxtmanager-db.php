<?php

class AdstxtManager_DB {

    private $DBVersion;

	public function __construct() {
        global $wpdb;

        $this->tableName = $wpdb->prefix . "adstxtmanager_data";

        $this->DBVersion = "1.0.0";
	}

    public function GetTableCreateStatement() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $createStatement = "CREATE TABLE " . $this->tableName . " (
            adstxtmanager_id int(10) NOT NULL,
            PRIMARY KEY  (adstxtmanager_id)
          ) " . $charset_collate . ";";

        return $createStatement;
    }

    public function GetTableVersion() {
        return $this->DBVersion;
    }



}
