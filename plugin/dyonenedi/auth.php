<?php
	namespace App\Plugin\Dyonenedi;
	
	use Lidiun\Request;
	use Lidiun\Render;
	use Lidiun\Path;
	use Lidiun\Conf;

	class Auth
	{
		public static function login($keepLogged=false, $saveInSession=false) {
			$code = Conf::$_conf['preset']['security_code'] . rand(0, 1000);
			$token = md5(uniqid($code, true));

			$_SESSION['SECURITY']["LOGGED"] = true;
			$_SESSION['SECURITY']["CODE"]   = $token;
			$_SESSION['SECURITY']["IP"]     = $_SERVER['REMOTE_ADDR'];
			
			if (is_array($saveInSession)) {
				foreach ($saveInSession as $key => $value) {
					$_SESSION['DATA'][$key] = $value;
				}
			} else {
				$_SESSION['DATA'] = false;
			}

			if ($keepLogged) {
				setcookie("cookSecurityCode", $_SESSION['SECURITY']["CODE"], time()+(60*60*24*120), Conf::$_conf['preset']['domain']);
				setcookie("cookSecurityIp", $_SESSION['SECURITY']["IP"], time()+(60*60*24*120), Conf::$_conf['preset']['domain']);
			}
		}

		public static function logout($redirect=true) {
			$conf = include_once('config/auth_config.php');
			
			unset($_SESSION["SECURITY"]);
			unset($_SESSION["DATA"]);

			setcookie("cookSecurityCode", "", time()-(60*60*24*120), Conf::$_conf['preset']['domain']); 
			setcookie("cookSecurityIp", "", time()-(60*60*24*120), Conf::$_conf['preset']['domain']);

			unset($_COOKIE["cookSecurityCode"]);
			unset($_COOKIE["cookSecurityIp"]);
			
			if ($redirect) {
				header('Location: '.Request::$_url['site'].$conf['redirect_unloged']);
			}
		}

		public static function getLogged() {
			if (
				!empty($_SESSION['SECURITY']) && !empty($_SESSION['SECURITY']["LOGGED"]) && !empty($_SESSION['SECURITY']["IP"]) 
				&& $_SESSION['SECURITY']["IP"] == $_SERVER['REMOTE_ADDR']
				|| !empty($_SESSION['SECURITY']["CODE"]) && !empty($_SESSION['SECURITY']["IP"]) && (!empty($_COOKIE["cookSecurityCode"]) && !empty($_COOKIE["cookSecurityIp"]) 
				&& $_COOKIE["cookSecurityCode"] == $_SESSION['SECURITY']["CODE"] && $_COOKIE["cookSecurityIp"] == $_SESSION['SECURITY']["IP"])
			) {
				return true;
			} else {
				unset($_SESSION["SECURITY"]);
				return false;
			}
		}

		public static function setSecurityLevel(){
			$conf = include_once('config/auth_config.php');

			if (array_search(Render::getRender(), $conf['logged']) !== false) {
				if (self::getLogged() == false) {
					if (file_exists(Path::$_path['render'].$conf['redirect_unloged'].'.php')){
						header('Location: '.Request::$_url['site'].$conf['redirect_unloged']);
					} else {
						throw new \Exception('I can\'t find "'.$conf['redirect_unloged'].'" in render');
					}
				}			
			} else if (array_search(Render::getRender(), $conf['unlogged']) !== false) {
				if (self::getLogged() == true) {
					if (file_exists(Path::$_path['render'].$conf['redirect_loged'].'.php')){
						header('Location: '.Request::$_url['site'].$conf['redirect_loged']);
					} else {
						throw new \Exception('I can\'t find "'.$conf['redirect_loged'].'" in render');
					}
				}
			} else if (array_search(Render::getRender(), $conf['whatever']) !== false) {
				// Doesn't matter about loged
			} else {
				throw new \Exception('I can\'t carry on without set access control to "'.Render::getRender().'" render in "auth_config.php"');
			}
		}
	}