<?php
	
	namespace Plugin\Dyonenedi;

	use Lidiun_Framework_v5\Render;
	use Lidiun_Framework_v5\Layout;

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