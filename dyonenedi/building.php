<?php

	namespace App\Plugin\Dyonenedi;

	use Lidiun\Database;

	Class Building
	{
		
		##############################################################################################
		############################################ SINGLETON #######################################
		##############################################################################################

		private static function singleton() {
			if (empty(self::$instance)) {
				self::$instance = new Building();
			}

			return self::$instance;
		} 

		####################################################################################################
		######################################## BUILD QUERY METHODS #######################################
		####################################################################################################

		// Erros message
		public  static $error        = false;
		private static $errorMessage = false;

		// Build query
		private static $instance    = false;
		private static $or          = 0;
		private static $wh          = 0;
		private static $end         = false;
		private static $sql         = '';

		/**
		* GET ERROR MESSAGE
		*
		*/
		public static function getErrorMessage(){
			return self::$errorMessage;
		}

		/**
		* GET QUERY
		*
		*/
		public static function getSql(){
			return self::$sql;
		}

		/**
		* SELECT BUILD CONSTRUCT
		*
		*/
		public static function select($column = 'id') {			
			self::$end    = false;
			self::$sql    = "";
			self::$wh     = 0;
			

			self::$sql = "SELECT " . $column . " ";
			
			return self::singleton();
		}

		/**
		* UPDATE BUILD CONSTRUCT
		*
		*/
		public static function update($table, $alias=false) {			
			self::$end    = false;
			self::$sql    = "";
			self::$wh     = 0;
			
			$alias = ($alias) ? "AS " . $alias . " ": "";

			if (!empty($table)) {
				self::$sql = "UPDATE " . $table . " " . $alias;
			}

			return self::singleton();
		}

		public static function set($data) {
			if (!empty($data) && is_array($data)) { 
				
				foreach ($data as $key => $value) {
					$values[] = $key . " = " . "'" . $value . "'";
				}
				$values = implode($values, ', ');
				
				self::$sql .= "SET " . $values . " ";
			}
			
			return self::singleton();
		}

		/**
		* DELETE BUILD CONSTRUCT
		*
		*/
		public static function delete() {			
			self::$end    = false;
			self::$sql    = "DELETE ";
			self::$wh     = 0;

			return self::singleton();
		}

		public static function proceed() {		
			return self::singleton();
		}

		public function from($table, $alias=false){
			$alias = ($alias) ? "AS " . $alias . " ": "";

			if (!empty($table) && is_string($table)) {
				self::$sql .= "FROM " . $table . " " . $alias;
			}
			
			return self::singleton();
		}

		public function with($with, $alias=false){
			$alias = ($alias) ? "AS " . $alias . " ": "";
			$with  = (is_array($with)) ? $with : [$with];

			if (!empty($with) && is_array($with)) { 
				foreach ($with as  $table => $on) {
					if (!empty($on) && is_array($on)) { 
						self::$sql .= "INNER JOIN " . $table . " " . $alias . "ON (";
						foreach ($on as  $key => $value) {
							$leftColumn = $key;
							$rightColumn = $value;
							$aux[] = $leftColumn . " = " . $rightColumn;
						}
						$on = implode($aux, " AND ");
						self::$sql .= $on . ") ";
					}
				}	
			}

			return self::singleton();
		}

		public function where($where){
			if (!empty($where) && is_array($where)) { 
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						$sign = $key;
						$leftColumn = key($value);
						$rightColumn = $value[$leftColumn];
					} else {
						$sign = "=";
						$leftColumn = $key;
						$rightColumn = $value;
					}
					
					if (self::$wh == 0) {
						self::$sql .= "WHERE (" . $leftColumn . " ".$sign." '" . $rightColumn . "'";
						self::$wh++;
						self::$end = true;
					} else if(self::$or) {
						self::$sql .= ") OR (" . $leftColumn . " ".$sign." '" . $rightColumn . "'";
						self::$or = false;
					} else {
						self::$sql .= " AND " . $leftColumn . " ".$sign." '" . $rightColumn . "'";
					}
				}
			}

			return self::singleton();
		}

		public function putOr(){
			self::$or = true;
			return self::singleton();
		}
		
		public function orderBy($orderBy){

			if (!empty($orderBy) && is_string($orderBy)) { 
				if (self::$end) {
					self::$end = false;
					self::$sql .= ") ORDER BY " . $orderBy . " ";
				} else {
					self::$sql .= "ORDER BY " . $orderBy . " ";
				}
			}
			
			return self::singleton();
		}
		
		public function groupBy($groupBy){
			
			if (!empty($groupBy) && is_string($groupBy)) { 
				if (self::$end) {
					self::$end = false;
					self::$sql .= ") GROUP BY " . $groupBy . " ";
				} else {
					self::$sql .= "GROUP BY " . $groupBy . " ";
				}
			}

			return self::singleton();
		}
		
		public function limit($limit){

			if (isset($limit) && is_int($limit)) { 
				if (self::$end) {
					self::$end = false;
					self::$sql .= ") LIMIT " . $limit . " ";
				} else {
					self::$sql .= "LIMIT " . $limit . " ";
				}
			}

			return self::singleton();
		}

		public function offset($offset){
			if (isset($offset) && is_int($offset)) { 
				if (self::$end) {
					self::$end = false;
					self::$sql .= ") OFFSET " . $offset . " ";
				} else {
					self::$sql .= "OFFSET " . $offset . " ";
				}
			}

			return self::singleton();
		}

		/**
		* INSERT BUILD CONSTRUCT
		*
		*/
		public static function insert() {			
			self::$end    = false;
			self::$sql    = "";
			
			return self::singleton();
		}
		
		public static function into($table) {
			if (!empty($table)) { 
				self::$sql = "INSERT INTO  " . $table . " ";
			}
			
			return self::singleton();
		}

		public static function values($data) {
			if (!empty($data) && is_array($data)) { 
				foreach ($data as $key => $value) {
					$columns[] = $key;
					$values[] = "'" . $value . "'";
				}

				$columns = "(" . implode($columns, ',') . ")";
				$values = "(" . implode($values, ',') . ")";
				
				self::$sql .= $columns . " VALUES " . $values . " ";
			}
			
			return self::singleton();
		}

		/**
		* EXECUTE BUILD METHODS
		*
		*/
		public function run($return='boolean'){
			if (self::$end) {
				self::$end = false;
				self::$sql .= ") ";
			}

			$result = Database::query(self::$sql, $return);
			if ($result) {
				return $result;
			} else {
				self::$error = true;
				self::$errorMessage = Database::getErrorMessage();
				
				return false;
			}
		}

		public function show($print=false){
			if (self::$end) {
				self::$end = false;
				self::$sql .= ") ";
			}
			if ($print) {
				echo "<pre>"; print_r(self::$sql);exit;
			} else {
				return self::$sql;
			}
		}

		/**
		* DATABASE METHODS
		*
		*/

		static public function autocommit($autocommit) {
			Database::autocommit($autocommit);
		}

		static public function commit() {
			Database::commit();
		}

		static public function rollback() {
			Database::rollback();
		}

		static public function getInsertId() {
			return Database::getInsertId();
		}
	}