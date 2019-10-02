<?php

	namespace Plugin\Dyonenedi;

	use Lidiun_Framework_v5\Request;

	Class Treatment 
	{	
		private static $rules;

		/**
		* Run treatment without return
		*
		*/
		public static function run(){
			self::$rules = include_once('config/treatment_config.php');
			$parameter = Request::getParameter();

			if (is_array($parameter)) {
				foreach ($parameter as $rules => $values) {
					if (is_array($values)) {
						if (array_key_exists($rules, self::$rules)) {	
							foreach ($values as $key => $value) {
								foreach (self::$rules[$rules] as $rule) {
									$parameter[$rules][$key] = addslashes(trim($rule($value)));
								}
							}
						}
					}
				}
				Request::setParameter($parameter);
			}
		}
	}