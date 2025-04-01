<?php
/**
 * Class which establishes a connection to the database
 */
require_once 'src/config.php';

class Model {
    public $db;

    public function __construct(){
      
        $this->db = new PDO(DB_TYPE.":host=".DB_HOST.";dbname=".DB_NAME,DB_USER,DB_PWD,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false]);
    }

    
}
?>