<?php
	
	namespace Plugin\Dyonenedi;

	use Lidiun\Render;
	use Lidiun\Layout;

	Class menu_active 
	{

		/**
		* Replace and active menu
		*
		*/
		public static function run(){
			$menu = include_once('config/menu_active_config.php');
			foreach ($menu as $key => $value) {
				if (Render::getRender() != strtolower($value)) {
					Layout::replaceMenu($key, '');
				} else {
					Layout::replaceMenu($key, 'active');
				}
			}
		}
	}