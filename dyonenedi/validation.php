<?php
	
	namespace App\Plugin\Dyonenedi;

	use Lidiun\Request;

	Class Validation 
	{
		private static $errorMessage = '';
		private static $rules;

		/**
		* Retorn error message from validation
		*
		*/
		public static function getErrorMessage(){
			return self::$errorMessage;
		}

		/**
		* Run validation and return boolean result
		*
		*/
		public static function run($rules=false){
			self::$rules = include_once('config/validation_config.php');
			self::$errorMessage = '';

			$parameter = Request::getParameter();

			if (is_array($parameter)) {
				if ($rules) {
					$rules = (!is_array($rules)) ? [0 => $rules] : $rules;
					foreach ($rules as $rule) {
						if (array_key_exists($rule, $parameter)) {
							$parameter[$rule] = $parameter[$rule];
						} else {
							self::$errorMessage = 'Key don\'t exist Request::$_parameter';
							return false;
						}	
					}
				}
				
				foreach ($parameter as $rules => $values) {
					if ($values && is_array($values)) {
						if (array_key_exists($rules, self::$rules)) {
							foreach (self::$rules[$rules] as $rule => $seted) {
								$ruleRun = 'validate'.ucfirst($rule);

								foreach ($values as $key => $value) {
									if (!self::$ruleRun($key,$value,$seted)) {
										return false;
									}
								}
							}
						} else {
							self::$errorMessage = 'Rule '.$rules.' don\'t exist in rules request_define';
							return false;
						}
					} else {
						self::$errorMessage = 'Request::$_parameter[$rule] must be an array.';
						return false;
					}
				}
			} else {
				self::$errorMessage = 'Request::$_parameter must be an array.';
				return false;
			}

			return true;
		}

		/**************************************************************
		*
		* Private static methods
		*
		***************************************************************/

		private static function validateFree($key,$value,$seted){
			return true;
		}

		private static function validateRequired($key,$value,$seted){
			if (!empty($value)) {
				return true;
			} else {
				self::$errorMessage	= $key.' is a required field.';
				return false;
			}
		}

		private static function validateLength($key,$value,$seted){
			$exp = (count(explode(',', $seted)) > 1) ? explode(',', $seted) : array(0 => 0, 1 => $seted);
			if (is_array($exp) && count($exp) == 2) {
				if (strlen($value) < $exp[0] || strlen($value) > $exp[1]) {
					self::$errorMessage = $key.' must have length between '.$exp[0].' and '.$exp[1].'.';
					return false;
				} else {
					return true;
				}
			} else {
				self::$errorMessage = 'Parameter error: '.__METHOD__;
				return false;
			}
		}

		private static function validateType($key,$value,$seted){
			if ($seted == 'string') {
				if (is_string($value)) {
					return true;
				} else {
					self::$errorMessage = $key.' is not a string.';
					return false;
				}
			} else if($seted == 'int') {
				$value = (int)$value;
				if (is_int($value)) {
					return true;
				} else {
					self::$errorMessage = $key.' is not a int.';
					return false;
				}
			} else {
				self::$errorMessage = 'Parameter error: '.__METHOD__;
				return false;
			}
		}

		private static function validateWord($key,$value,$seted){
			if (preg_match('/[A-Za-zÀ-ùÁ-ú\s]/', $value) == $seted) {
				return true;
            } else {
             	$not = ($seted) ? '' : 'not';
             	self::$errorMessage = $key.' must have '.$not.' word.';
                return false;
            }
		}

		private static function validateSpace($key, $value,$seted){
			if (preg_match('/[\s]/', $value) == $seted) {
				return true;
            } else {
             	$not = ($seted) ? '' : 'not ';
             	self::$errorMessage = $key.' must have '.$not.'a space.';
                return false;
            }
		}

		private static function validateCaracter($key,$value,$seted){
			if (preg_match("/[!\"#$%&'()*+,-.\/:;?@[\\\]_`{|}~]/", $value) == $seted) {
				return true;
            } else {
             	$not = ($seted) ? '' : 'not ';
             	self::$errorMessage = $key.' must have '.$not.'a caracter.';
                return false;
            }
		}

		private static function validateNumber($key,$value,$seted){
			if (preg_match('/[0-9]/', $value) == $seted) {
				return true;
            } else {
             	$not = ($seted) ? '' : 'not ';
             	self::$errorMessage = $key.' must have '.$not.'a number.';
                return false;
            }
		}

		private static function validateExpression($key,$value,$seted){
			if (preg_match('/^'.$seted.'$/', $value)) {
				return true;
            } else {
             	$not = ($seted) ? '' : 'not ';
             	self::$errorMessage = $key.' is not valid.';
                return false;
            }
		}
	}