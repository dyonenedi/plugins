<?php
	
	namespace App\Plugin\Dyonenedi;

	use Lidiun\Database;

	Class Crud 
	{
		private $table;
		private $colunm;
		public  $query;
		public  $insert_id;
		public  $callback;
		public  $messageError;

		public function __construct($table) {
			$this->table = $table;

			$result = Database::query("SHOW COLUMNS FROM ".$this->table, 'array');
			foreach ($result as $key => $value) {
				$this->colunm[] = $value['Field']; 	
			}
		}

		public function create() {
			$error = false;
			$columns = get_object_vars($this);
			
			foreach ($columns as $key => $val) {
				if (is_string($key) && (is_null($val) || is_string($val) || is_float($val) || is_int($val) || is_numeric($val)) && $key != 'query' && $key != 'insert_id' && $key != 'callback' && $key != 'messageError' && $key != 'table' && $key != 'colunm') {
					if (in_array($key, $this->colunm)) {
						$colunm[] = $key;
						$value[] = "'".$val."'";
					} else {
						$colunm = $key;
						$error = true;
						break;
					}
				}
			}

			if (!empty($colunm) &&!empty($value) && is_array($colunm) && is_array($value)) {
				if (!$error) {
					$colunm = implode(',', $colunm);
					$value  = implode(',', $value);

					$this->query = "
						INSERT INTO ".$this->table." 
						(".$colunm.") 
						VALUES 
						(".$value.")
					";

					if (Database::query($this->query)) {
						$this->insert_id = Database::getInsertId();
						$this->callback = true;
					} else {
						$this->callback = false;
						$this->messageError = 'Error Query: '.$this->query;
					}
				} else {
					$this->callback = false;
					$this->messageError = 'Colunm '.$colunm.' do not exist.';
				}
			} else {
				$this->callback = false;
				$this->messageError = 'Do not exist any colunm.';
			}
		}

		public function read($select=false,$param=false,$inner=false,$end='') {
			$select = empty($select) ? '*': $select; 
			$join   = "";
			$where  = "1";

			if ($param && is_array($param)) {
				foreach ($param as $key => $val) {
					if (strtoupper($val) == 'IS NULL' || strtoupper($val) == 'IS NOT NULL') {
						$where .= " AND ".$key." ".$val;
					} else {
						$where .= " AND ".$key."='".$val."'";
					}
				}
			}

			if ($inner && is_array($inner)) {
				foreach ($inner as $val) {
					if (!empty($val['type'])) {
						$type = $val['type'];
					} else {
						$type = 'INNER';
					}
					$join .= " ".$type." JOIN ".$val['table']." ON ".$val['on']." ";
				}
			}

			$this->query = "
				SELECT ".$select." FROM ".$this->table." AS A 
				".$join." 
				WHERE ".$where."
				 ".$end."
			";

			$result = Database::query($this->query, 'array');
			if ($result) {
				$user = array();
				foreach ($result as $key => $value) {
					$user[$key] = $value;
				}

				$this->callback = true;

				return $user;
			} else {
				$this->callback = false;
				$this->messageError = 'Error Query: '.$this->query;
			}
		}

		public function update($param) {
			$columns = get_object_vars($this);
			foreach ($columns as $key => $val) {
				if ((is_string($val) || is_numeric($val)) && $key != 'table' && $key != 'colunm' && $key != 'query' && $key != 'callback') {
					if (in_array($key, $this->colunm)) {
						$value[] = $key."='".$val."'";
					} else {
						$value = '';
						break;
					}
				}
			}

			if (!empty($value)) {
				$value  = implode(',', $value);

				$where = "1";
				foreach ($param as $key => $val) {
					$where .= " AND ".$key."='".$val."'";
				}				

				$this->query = "
					UPDATE ".$this->table." 
					SET ".$value."
					WHERE ".$where."
				";

				if (Database::query($this->query)) {
					$this->callback = true;
				} else {
					$this->callback = false;
					$this->messageError = 'Error Query: '.$this->query;
				}
			} else {
				$this->callback = false;
				$this->messageError = 'Do not exist colunm to update.';
			}
		}

		public function delete($param) {
			$where = "1";
			foreach ($param as $key => $val) {
				$where .= " AND ".$key."='".$val."'";
			}

			$this->query = "
				DELETE FROM ".$this->table." 
				WHERE ".$where."
			";

			if ($result = Database::query($this->query)) {
				$this->callback = true;
			} else {
				$this->callback = false;
				$this->messageError = 'Error Query: '.$this->query;
			}
		}

		public function startTransaction(){
			Database::autocommit(false);
		}

		public function commit() {
			Database::commit();
		}

		public function rollback() {
			Database::rollback();
		}
	}