<?php

/*

	Date: 21/03/2019
	Author: Tristan Tyler
	Purpose: A product class to represent a product stored in a Mysql database.
	The class contains functions to access information from a Mysql database using
	Mysqli and prepared statements.
	Version: 1.

*/

class Product {
	
	public $loadSuccessful;
	public $error;
	public $details;
	protected $files;
	protected $dbAttributes;
	protected static $dbConn;
	
	//Constructor method.
	
	public function __construct($searchParam, $dbAttributes, $dbConn) {
		$this->set_DB_Attributes($dbAttributes);
		self::$dbConn = $dbConn;
		$this->set_details($searchParam);
	}
	
	//Constructor optional input variables initialisation.
	
	protected function set_DB_Attributes($dbAttributes) {
		if(isset($dbAttributes) && is_array($dbAttributes)) {
			$this->dbAttributes = $dbAttributes;
		}
		else {
			$this->dbAttributes = array("TableName"=>"Products", "UniqueIdentifierField"=>"id", "FieldsToReturn"=>"*");
		}
	}
	
	//protected methods. 
	
	protected function set_details($searchParam) {
		$searchParam = $this->check_params($searchParam);
		$query = "SELECT * From ".$this->dbAttributes["TableName"]." where ".$this->construct_where_string($searchParam);
		$this->details = $this->run_query($query, $searchParam);
		if(count($this->details) == 0) { $this->loadSuccessful = false; }
		else { $this->loadSuccessful = true; }
	}
	
	protected function check_result($query, $error) {
				$this->error[]['Query'] = $query;
				$this->error[]['Error'] = $error;
	}
	
	protected function run_query($query, $searchParam) {
		$paramList = $this->construct_param_list($searchParam);
		$paramTypes = $this->get_param_types($searchParam);
		return $this->get_database_result($query, $paramTypes, $paramList);
	}
	
	protected function construct_param_list($params) {
		$return = array();
		for($c = 0; $c < count($params); $c++) {
			$return[$c] = $params[$c]["fieldValue"];
		}
		return $return;
	}
	
	protected function construct_where_string($params) {
		$whereArr = array();
		for($c = 0; $c < count($params); $c++) {
			$whereArr[$c] = $params[$c]["fieldName"]." = ?";
		}
		return implode("AND", $whereArr);
	}
	
	protected function check_params($array) {
		reset($array);
		if(is_string(key($array))) {
			$arr[] = $array;
			return $arr;
		}
		else {
			return $array;
		}
	}
	
	protected function get_param_types($params) {
		$paramString = "";
		for($c = 0; $c < count($params); $c++) {
			$paramString .= $params[$c]["fieldType"];
		}
		return $paramString;
	}
	
	protected function get_database_result($query, $paramTypes, $paramList) {
		$result = array();
		if($stmt = self::$dbConn->prepare($query)) {
			$this->bind_param_array($paramTypes, $paramList, $stmt);
			if($stmt->execute()) {
				if($stmt->store_result()) {
					$this->bind_result_array($fields, $stmt);
					if($stmt->num_rows == 1) {
						$stmt->fetch();
						foreach($fields as $fieldName => $fieldValue) { $row[$fieldName] = $fieldValue; }
						$result = $row;
					}
					elseif($stmt->num_rows > 1) {
						while($stmt->fetch()) {
							foreach($fields as $fieldName => $fieldValue) { $row[$fieldName] = $fieldValue; }
							$result[] = $row;
						}
					}
					elseif($stmt->num_rows == 0) {
						$this->check_result($query, "No records found.");
					}
					$stmt->close();
				}
				else { $this->check_result($query, self::$dbConn->error); }
			}
			else { $this->check_result($query, self::$dbConn->error); }
		}
		else { $this->check_result($query, self::$dbConn->error); }
		return $result;
	}
	
	protected function bind_param_array($paramTypes, &$paramList, $stmt) {
		$params[0] = $paramTypes;
		for($c = 1; $c <= count($paramList); $c++) {
			$params[$c] = &$paramList[$c - 1];
		}
		call_user_func_array(array($stmt, "bind_param"), $params);
	}
	
	protected function bind_result_array(&$fields, $stmt) {
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field()) { 
			$params[] = &$fields[$field->name]; 
		} 
		call_user_func_array(array($stmt, "bind_result"), $params);
	}
	
	//Public Methods
}

?>