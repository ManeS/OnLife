<?php
/**
 * DriverMysqli
 * 
 * Драйвер базы данных для MySQLi
 * 
 * @package Phenol2.com
 * @author LestaD
 * @copyright 2013
 * @version 1
 * @access public
 */
final class DriverMysqli {
	private $link;
	
	public function __construct($hostname, $username, $password, $database) {
		@$this->link = new mysqli($hostname, $username, $password, $database);
		if ( $this->link->connect_error ) {
      		trigger_error('Error: Could not make a database link using ' . $username . '@' . $hostname);
      		die();
    	}

    	//if (!mysql_select_db($database, $this->link)) {
      	//	trigger_error('Error: Could not connect to database ' . $database);
    	//}
  	}
  	
  	public function encoding( $encoding )
  	{
  		$this->link->real_query("SET NAMES '$encoding'");
		$this->link->real_query("SET CHARACTER SET $encoding");
		$this->link->real_query("SET CHARACTER_SET_CONNECTION=$encoding");
		$this->link->real_query("SET SQL_MODE = ''");
  	}
		
  	public function query($sql) {
  		
  		$ret = $this->link->real_query($sql);
		$resource = $this->link->store_result();

		if ( $resource || ($this->link->errno < 1) ) {
			if ( 1 ) {
				
				if ( is_bool( $resource ) )
				{
					return $resource;
				}
				
				$i = 0;
    			
				$data = array();
		
				while ($result = $resource->fetch_assoc()) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				$resource->free_result();
				
				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;
				
				unset($data);
				
				return $query;	
    		} else {
				return true;
			}
		} else {
			echo "<pre>";
			print_r($sql . " - ");
			print_r($resource);
			echo "</pre>";
			
			trigger_error('Error: ' . $this->link->connect_error . '<br />Error No: ' . $this->link->connect_errno . '<br />SQL: <pre>' . $sql . '</pre>');
			exit();
    	}
  	}
	
	public function escape($value) {
		return $this->link->real_escape_string($value);
	}
	
  	public function countAffected() {
    	return $this->link->affected_rows;
  	}

  	public function getLastId() {
    	return $this->link->insert_id;
  	}	
	
	public function __destruct() {
		if ( $this->link ) $this->link->close();
	}
}
