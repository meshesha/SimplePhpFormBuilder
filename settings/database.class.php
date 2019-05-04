<?php
/**
 * Mysqli database class - one connection.
 */
$isSetDBclass = true;
class Database {
	private $_connection;
	private static $_instance; //The single instance
	private $_host = "localhost";
	private $_username = "root";
	private $_password = "koll34ll";
    private $_database;
    
	// Constructor
	public function __construct($db_name) {
        $this->_database = $db_name;
		$this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
        mysqli_set_charset($this->_connection, "utf8");
		// Error handling
		if(mysqli_connect_error()) {
			trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(), E_USER_ERROR);
		}
	}
	// Magic method clone is empty to prevent duplication of connection
	private function __clone() { }
	// Get mysqli connection
	public function getConnection() {
		return $this->_connection;
	}
}
/**
 *  $db = new Database("db_name");
 *  $mysqli = $db->getConnection(); 
 *  $sql_query = "SELECT foo FROM .....";
 *  $result = $mysqli->query($sql_query);
 */
?>