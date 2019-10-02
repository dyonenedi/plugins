<?php

	namespace Plugin\Dyonenedi;

	use Lidiun_Framework_v5\Layout;
	use Lidiun_Framework_v5\Link;
	use Lidiun_Framework_v5\Path;

	Class Tools
	{
		public static function getPagination($total, $page, $limit, $html, $link=''){
			$link = (substr($link, -1) == '/') ? $link: $link.'/';

			$end = ceil($page / $limit) * $limit;
			$start = $end - ($limit - 1);
			$end = ($end > $total) ? $total: $end;

			$i = $start;
			$numbers = '';
			while ($i <= $end) {
				if ($i == $page) {
					$numbers .= '<li class="active"><a href="'.$link.$i.'/">'.$i.' <span class="sr-only">(current)</span></a></li>';
				} else {
					$numbers .= '<li><a href="'.$link.$i.'/">'.$i.'</a></li>';
				}
				$i++;
			}

			if ($numbers) {
				$blockBack = ($start == 1) ? 'class="disabled"': '';
				$blockNext = ($end == $total) ? 'class="disabled"': '';

				$back = ($start == 1) ? '<a>«</a>': '<a href="'.$link.($start - 1).'/">«</a>';
				$next = ($end == $total) ? '<a>»</a>': '<a href="'.$link.($end + 1).'/">»</a>';

				$pag = $html;
				$pag = str_replace('<%NUMBER%>', $numbers, $pag);
				$pag = str_replace('<%BLOCK_BACK%>', $blockBack, $pag);
				$pag = str_replace('<%BLOCK_NEXT%>', $blockNext, $pag);
				$pag = str_replace('<%BACK%>', $back, $pag);
				$pag = str_replace('<%NEXT%>', $next, $pag);
			} else {
				$pag = '';
			}

			return $pag;
		}

		public static function getDate($date,$style=false){
			if ($style) {
				$day = date("d", strtotime($date));
				$month = date("m", strtotime($date));
				$year = date("Y", strtotime($date));
				
				if ($year == date("Y") && $month == date("m") && (date("d") - $day) < 7 ) {
					$day = date("w", strtotime($date));
					$week = [
						0 => 'Domingo',
						1 => 'Segunda Feira', 
						2 => 'Terça Feira', 
						3 => 'Quarta Feira', 
						4 => 'Quinta Feira',
						5 => 'Sexta Feira',
						6 => 'Sábado',
					];

					$date = $week[$day];
				} else if ($year == date("Y") && $month == date("m")) {
					$day = date("d") - $day;
					$date = "à ".$day." dias";
				} else if ($year == date("Y") && (date("m") - $month) <= 1) {
					$date = "mês passado";
				} else {
					$date = "à mais de um mês";
				}
			} else {
				$date = date("d/m/Y H:i", strtotime($date));
			}
			
			return $date;
		}

		public static function encodePhone($string) {
			preg_match("/(([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9]))/", $string, $match);
			if (is_array($match) && count($match)) {
				$string = preg_replace("(([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9])+([0-9]))","****-****", $string);
			}

			preg_match("/(([0-9])+([0-9])+([0-9])+([0-9]))/", $string, $match);
			if (is_array($match) && count($match)) {
				$string = preg_replace("(([0-9])+([0-9])+([0-9])+([0-9]))","****", $string);
			}

			preg_match("/([0-9]+[0-9]+-)/", $string, $match);
			if (is_array($match) && count($match)) {
				$string = preg_replace("([0-9]+[0-9]+-)","(**) ", $string);
			}

			preg_match("/(\([0-9]+[0-9]\))/", $string, $match);
			if (is_array($match) && count($match)) {
				$string = preg_replace("(\([0-9]+[0-9]\))","(**)", $string);
			}

			return $string;
		}

		// Treatment of link
		public static function searchTreatment($string) {
			if (!is_numeric($string)) {
				$string = str_replace(array("<", ">", "\\", "/", "=", "!=", "?", "%", "*"), "", $string);
				$string = addslashes(strip_tags(trim($string)));

				return $string;
			} else {
				return $string;
			}
		}

		// Cut  strings
		public static function cutString($string,$lenght,$cutWord=false){
			$string = utf8_decode($string);
			
			if (strlen($string) > $lenght) {
				if (!$cutWord) {
					$newString = substr($string, 0, $lenght-3);
					$newString .= "...";
				} else {
					$newString = substr($string, 0, $lenght);
					$pos = strrpos($newString,' ');
					if ($pos) {
						$newString = substr($string, 0, strrpos($newString,' '));
						$newString = trim($newString);
						$word = substr($newString, strrpos($newString,' '));
						$word = strtolower(trim($word));
						if ($word == "da" || $word == "das" || $word == "de" || $word == "-") {
							$newString = substr($newString, 0, strrpos($newString,' '));
						}
					} else {
						$newString = substr($string, 0, ($lenght-3));
						$newString .= "...";
					}
				}
			} else {
				$newString = $string;
			}
			return utf8_encode($newString);
		}

		// Indentify link in a string text and put btween a link
		public static function identifyLink($text)  {
			preg_match("/(http:\/\/|https:\/\/)/", $text, $match);
			if (is_array($match) && count($match)) {
				$text = preg_replace("(((http:\/\/|https:\/\/)?([a-zA-Z0-9-_]{2,}[\.]{1})|(http:\/\/|https:\/\/){1})+([a-zA-Z0-9-_]{2,}[\.]{1})+([a-zA-Z\.]{2,6})([\/|\?]{1}[a-zA-Z0-9_/\-\?\.#$%&;*+,=]*)?)",html_entity_decode("<a href=\"\\0\" class=\"link\" target=\"_blank\">\\0</a>",ENT_COMPAT), $text);
				return $text;
			} else {
				$text = preg_replace("(((http:\/\/|https:\/\/)?([a-zA-Z0-9-_]{2,}[\.]{1})|(http:\/\/|https:\/\/){1})+([a-zA-Z0-9-_]{2,}[\.]{1})+([a-zA-Z\.]{2,6})([\/|\?]{1}[a-zA-Z0-9_/\-\?\.#$%&;*+,=]*)?)",html_entity_decode("<a href=\"http://\\0\" class=\"link\" target=\"_blank\">\\0</a>",ENT_COMPAT), $text);
				return $text;
			}
		}

		// Identify Hashtag in a string text and put color blue
		public static function identifyHashtag($text)  {
			preg_match("/(#)+([a-zA-Z0-9çÇáéíóúýÁÉÍÓÚÝàèìòùÀÈÌÒÙãõñäëïöüÿÄËÏÖÜÃÕÑâêîôûÂÊÎÔÛ]+)/", $text, $match);
			if (is_array($match) && count($match)) {
				$text = preg_replace("((#)+([a-zA-Z0-9çÇáéíóúýÁÉÍÓÚÝàèìòùÀÈÌÒÙãõñäëïöüÿÄËÏÖÜÃÕÑâêîôûÂÊÎÔÛ]+))",html_entity_decode("<span class=\"color_blue\">\\0</span>",ENT_COMPAT), $text);
				return $text;
			} else {
				return $text;
			}
		}

		// Find youtube id in a link on the text
		public static function findIdVideo($text) {
			preg_match("/(http:\/\/|https:\/\/)?(www.)?(youtube\.)+(com|com.br{1})+(\/watch\?v=)+([a-zA-Z0-9_-]+)/", $text, $match);
			if (is_array($match) && isset($match[0]) && $match[0]) {
				$exMatch = explode("/watch?v=", $match[0]);
				return $exMatch[1];
			} else {
				preg_match("/(http:\/\/|https:\/\/)?(youtu\.)+(be\/{1})+([a-zA-Z0-9_-]+)/", $text, $match);
				if (is_array($match) && isset($match[0])) {
					$exMatch = explode("youtu.be/", $match[0]);
					return $exMatch[1];
				} else {
					return false;
				}
			}
		}

		// Find youtube link on the text and replace
		public static function findVideoReplace($text) {
			preg_match("/(http:\/\/|https:\/\/)?(www.)?(youtube\.)+(com|com.br{1})+(\/watch\?v=)+([a-zA-Z0-9_-\W]+)/", $text, $match);
			if (is_array($match) && isset($match[0]) && $match[0]) {
				$text = str_replace($match[0], "", $text);
				return $text;
			} else {
				preg_match("/(http:\/\/|https:\/\/)?(youtu\.)+(be\/{1})+([a-zA-Z0-9_-\W]+)/", $text, $match);
				if (is_array($match) && isset($match[0])) {
					$text = str_replace($match[0], "", $text);
					return $text;
				} else {
					return $text;
				}
			}
		}

		public static function treatUrl($url) {
			$url = str_replace('/', '_', $url);
			$url = str_replace(' ', '-', $url);

			return preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $url));
		}

		##############################################################################
		############################# FOR THIS PROJECT ###############################
		##############################################################################

		public static function getUserPhoto($userId, $size=1) {
			if (file_exists(Path::$_path['user'].$userId.'/profile/'.$size.'/user.jpg')) {
				$photo = Link::$_link['user'].$userId.'/profile/'.$size.'/user.jpg';
			} else {
				$photo = Link::$_link['default'].$size.'/user.jpg';
			}

			return $photo;
		}

		public static function getPetPhoto($userId, $itemId, $i, $size=3, $original=false) {
			$format = ($original) ? 'original': 'cuted';

			if (file_exists(Path::$_path['user'].$userId.'/pet/'.$format.'/'.$itemId.'/pet_'.$i.'.jpg')) {
		 		$photo = Link::$_link['user'].$userId.'/pet/'.$format.'/'.$itemId.'/pet_'.$i.'.jpg';
		 	} else {
		 		$photo = Link::$_link['default'].$size.'/pet.jpg';
		 	}

		 	return $photo;
		}
	}
?>