<?php

	namespace App\Plugin\Dyonenedi;
	
	use Lidiun\Conf;

	Class Encrypt
	{
		public static function encodePassword($string, $password=false){
			$password = ($password) ? $password: Conf::$_conf['preset']['security_code'];
			$password = sha1($password);
			$string = sha1($string);
			$count = (strlen($password) <= 16) ? strlen($password) : 16;
			$password = strrev(substr($password, 0, $count-1));

			for ($i=$count; $i > 0; $i--) { 
				$j = $i-1;
				$ex[] = substr($password, $j, $i);
			}

			$i = 1;

			foreach($ex as $value){
				if ($i) {
					$string = $string . $value;
					$i = 0;
				} else {
					$string = $value . $string;
					$i = 1;
				}	
			}

			$string = sha1(trim($string));

			return $string;
		}	
	}