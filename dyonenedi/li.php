<?php
	namespace Plugin\Dyonenedi;
	
	use Lidiun\Path;
	use Lidiun\Database;

	class Li
	{
		public static function generate($table, $filename, $path=false, $inline=true) {
			$contentAux = '';
			
			$form       = self::getHtml('li/form');
			$content    = self::getHtml('li/form-content');
			$inline     = ($inline) ? '10': '12';
			
			$colunms    = Database::query("SHOW COLUMNS FROM ".$table, 'array');
			if (!empty($colunms)) {
				foreach ($colunms as $key => $colunm) {
					if ($colunm['Key'] != 'PRI') {
						
						$required = ($colunm['Null'] = 'NO') ? 'required': '';
						
						$type = substr($colunm['Type'], 0, 3);
						if ($type == 'dat' || $type == 'tim') {
							$type = "date";
						} else if ($type == 'cha' || $type == 'var') {
							$type = "text";
						} else if (($type == 'tin' && $colunm['Type'] == 'tinytext') || $type == 'tex' || ($type == 'med' && $colunm['Type'] == 'mediumtext') || ($type == 'lon' && $colunm['Type'] == 'longtext')) {
							$type = "textarea";
						} else if (($type == 'tin' && $colunm['Type'] == 'tinyblob') || $type == 'blo' || ($type == 'med' && $colunm['Type'] == 'mediumblob') || ($type == 'lon' && $colunm['Type'] == 'longblob')) {
							$type = "radius";
						} else if ($type == 'enu') {
							$type = "select";
						} else if ($type == 'tin' || $type == 'sma' || $type == 'med' || $type == 'int' || $type == 'big' || $type == 'flo' || $type == 'rea' || $type == 'dec' || $type == 'num' || $type == 'yea') {
							$type = 'number';
						}
						
						if ($type == "textarea") {
							$element = self::getHtml('li/form-element-textarea');
							$element = str_replace('<%ID%>', strtolower($colunm['Field']), $element);
							$element = str_replace('<%NAME%>',strtolower($colunm['Field']), $element);
							$element = str_replace('<%REQUIRED%>', $required, $element);
						} else if ($type == "select") {
							$select = substr($colunm['Type'], 5, -2);
							$select = explode(',', $select);
							$option = "";
							foreach ($select as $value) {
								$value   = str_replace("'", '', $value);
								$option .= '<option value="'.$value.'">'.ucfirst(strtolower($value)).'</option>';
							}

							$element = self::getHtml('li/form-element-select');
							$element = str_replace('<%ID%>', strtolower($colunm['Field']), $element);
							$element = str_replace('<%NAME%>',strtolower($colunm['Field']), $element);
							$element = str_replace('<%REQUIRED%>', $required, $element);
							$element = str_replace('<%OPTION%>', $option, $element);
						} else {
							$element = self::getHtml('li/form-element-input');
							$element = str_replace('<%TYPE%>', $type, $element);
							$element = str_replace('<%ID%>', strtolower($colunm['Field']), $element);
							$element = str_replace('<%NAME%>',strtolower($colunm['Field']), $element);
							$element = str_replace('<%REQUIRED%>', $required, $element);
						}
						
						
						$contentAux .= $content;
						$contentAux = str_replace('<%LABEL%>', ucfirst($colunm['Field']), $contentAux);
						$contentAux = str_replace('<%INLINE%>', $inline, $contentAux);
						$contentAux = str_replace('<%ELEMENT%>', $element, $contentAux);
						$contentAux = str_replace('<%FOR%>', strtolower($colunm['Field']), $contentAux);
					}
				}

				$form = str_replace('<%NAME%>', '_'.$filename, $form);
				$form = str_replace('<%ID%>', '_'.$filename, $form);
				$form = str_replace('<%CONTENT%>', $contentAux, $form);
				
				$path = (!empty($path)) ? $path: Path::$_path['segment'];
				$path = (strrpos($path, '/')) ? $path: $path.'/';
				$file = $path.$filename.'.html';
				file_put_contents($file, $form);

				return true;
			} else {
				return false;
			}
		}

		private static function getHtml($file) {
			$file = __DIR__.'/'.$file.'.html';
			$html = file_get_contents($file);
			return $html;
		}
	}