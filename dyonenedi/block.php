<?php

	namespace Plugin\Dyonenedi;

	Class Block
	{
		public static function mount($data, $replace, $html){
			$list = '';
			if (!empty($replace) && is_array($replace)) {
				foreach ($data as $row) {
					$list .= $html;
					foreach ($replace as $key => $value) {
						$list = str_replace('<%'.strtoupper($key).'%>', $row[$value], $list);
					}
				}
			}

			return $list;
		}
	}